<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Department;
use App\Models\DocType;
use App\Models\DocItem;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /* =========================================================
     |  UTIL: Mapping hak akses → daftar department id
     |=========================================================*/

    /** Dept yang user BISA LIHAT (view) — longgar. */
    private function departmentIdsForView($user)
    {
        if ($user->role === 'super_admin') {
            return Department::pluck('id');
        }

        // 1) Admin Dept
        $deptFromAdmin = $user->adminDepartments()->allRelatedIds(); // id only

        // 2) ACL scope=department (view/edit)
        $deptFromAclDept = $user->acl()->where('scope_type','department')->pluck('department_id');

        // 3) ACL scope=doc_type → map ke dept
        $docTypeIds = $user->acl()->where('scope_type','doc_type')->pluck('scope_id');
        $deptFromDocTypes = collect();
        if ($docTypeIds->isNotEmpty()) {
            // asumsi DocType hasManyToMany Departments
            $deptFromDocTypes = Department::whereHas('docTypes', function($q) use ($docTypeIds) {
                $q->whereIn('doc_types.id', $docTypeIds);
            })->pluck('id');
        }

        // 4) ACL scope=item → map via DocItem.department_id
        $itemIds = $user->acl()->where('scope_type','item')->pluck('scope_id');
        $deptFromItems = collect();
        if ($itemIds->isNotEmpty()) {
            $deptFromItems = DocItem::whereIn('id', $itemIds)->pluck('department_id');
        }

        return collect()
            ->merge($deptFromAdmin)
            ->merge($deptFromAclDept)
            ->merge($deptFromDocTypes)
            ->merge($deptFromItems)
            ->filter()
            ->unique()
            ->values();
    }

    /** Dept yang user BISA EDIT — ketat (harus admin dept ATAU ACL can_edit=1). */
    private function departmentIdsForEdit($user)
    {
        if ($user->role === 'super_admin') {
            return Department::pluck('id');
        }

        // 1) Admin Dept = full edit
        $deptFromAdmin = $user->adminDepartments()->allRelatedIds();

        // 2) ACL dept yang explicitly can_edit
        $deptFromAclDept = $user->acl()
            ->where('scope_type','department')
            ->where('can_edit', true)
            ->pluck('department_id');

        // 3) ACL doc_type can_edit → map ke dept
        $docTypeIds = $user->acl()
            ->where('scope_type','doc_type')
            ->where('can_edit', true)
            ->pluck('scope_id');
        $deptFromDocTypes = collect();
        if ($docTypeIds->isNotEmpty()) {
            $deptFromDocTypes = Department::whereHas('docTypes', function($q) use ($docTypeIds) {
                $q->whereIn('doc_types.id', $docTypeIds);
            })->pluck('id');
        }

        // 4) ACL item can_edit → map ke dept
        $itemIds = $user->acl()
            ->where('scope_type','item')
            ->where('can_edit', true)
            ->pluck('scope_id');
        $deptFromItems = collect();
        if ($itemIds->isNotEmpty()) {
            $deptFromItems = DocItem::whereIn('id', $itemIds)->pluck('department_id');
        }

        return collect()
            ->merge($deptFromAdmin)
            ->merge($deptFromAclDept)
            ->merge($deptFromDocTypes)
            ->merge($deptFromItems)
            ->filter()
            ->unique()
            ->values();
    }

    /** Departemen untuk dropdown editor (Tambah/Edit) — pakai hak EDIT. */
    private function allowedDepartmentsForEditor($user)
    {
        if ($user->role === 'super_admin') {
            return Department::orderBy('name')->get();
        }
        $ids = $this->departmentIdsForEdit($user);
        return $ids->isEmpty()
            ? collect()
            : Department::whereIn('id', $ids)->orderBy('name')->get();
    }

    /* =========================================================
     |  LIST
     |=========================================================*/
    public function index(Request $request)
    {
        $u = $request->user();

        $q = Document::with(['department','docType','item'])
            ->latest('published_at');

        if ($u->role !== 'super_admin') {
            // List pakai hak VIEW (lebih longgar)
            $deptIds = $this->departmentIdsForView($u);
            $q->whereIn('department_id', $deptIds->isEmpty() ? [-1] : $deptIds);
        }

        if ($request->filled('q')) {
            $search = trim((string)$request->input('q'));
            if ($search !== '') {
                $q->where(function($w) use ($search){
                    $w->where('title','like',"%{$search}%")
                      ->orWhere('summary','like',"%{$search}%");
                });
            }
        }
        if ($request->filled('status')) {
            $q->where('status', (string)$request->input('status'));
        }

        $docs = $q->paginate(20)->withQueryString();
        return view('admin.documents.index', compact('docs'));
    }

    /* =========================================================
     |  CREATE
     |=========================================================*/
    public function create(Request $request)
    {
        $u = $request->user();

        $departments = $this->allowedDepartmentsForEditor($u); // EDIT
        $docTypes    = DocType::orderBy('name')->get();
        // Item bisa kamu filter via AJAX berdasarkan dept+doctype
        $items       = DocItem::with(['department','docType'])->orderBy('name')->get();

        return view('admin.documents.create', compact('departments','docTypes','items'));
    }

    /* =========================================================
     |  STORE
     |=========================================================*/
    public function store(Request $r)
    {
        $data = $r->validate([
            'department_id' => 'required|exists:departments,id',
            'doc_type_id'   => 'required|exists:doc_types,id',
            'doc_item_id'   => 'nullable|exists:doc_items,id',
            'title'         => 'required|string|max:200',
            'slug'          => 'nullable|string|max:200|alpha_dash|unique:documents,slug',
            'summary'       => 'nullable|string',
            'file'          => 'required|file|max:20480',
            'status'        => 'required|in:draft,open,archived',
            'published_at'  => 'nullable|date',
            'is_active'     => 'nullable|boolean',
        ]);

        $deptId = (int)$data['department_id'];
        $typeId = (int)$data['doc_type_id'];
        $itemId = isset($data['doc_item_id']) ? (int)$data['doc_item_id'] : null;

        // Enforce EDIT access
        abort_unless($r->user()->hasEditAccess($deptId, $typeId, $itemId), 403);

        // Upload file
        $path = $r->file('file')->store('documents/'.date('Y/m'),'public');
        $ext  = strtolower($r->file('file')->getClientOriginalExtension());

        $slug = !empty($data['slug'])
            ? Str::slug($data['slug'])
            : Str::slug($data['title']).'-'.Str::random(6);

        $doc = Document::create([
            ...Arr::only($data, ['department_id','doc_type_id','doc_item_id','title','summary','status']),
            'slug'         => $slug,
            'file_path'    => $path,
            'file_ext'     => $ext,
            'published_at' => $data['published_at'] ?? ($data['status']==='open' ? now() : null),
            'uploaded_by'  => $r->user()->id,
            'is_active'    => $r->boolean('is_active'),
        ]);

        return redirect()->route('admin.documents.index')->with('ok','Dokumen tersimpan.');
    }

    /* =========================================================
     |  EDIT
     |=========================================================*/
    public function edit(Request $request, Document $document)
    {
        // Enforce EDIT pada dokumen saat ini
        abort_unless(
            $request->user()->hasEditAccess(
                (int)$document->department_id,
                (int)$document->doc_type_id,
                $document->doc_item_id ? (int)$document->doc_item_id : null
            ),
            403
        );

        $u = $request->user();
        $departments = $this->allowedDepartmentsForEditor($u); // EDIT
        $docTypes    = DocType::orderBy('name')->get();
        $items       = DocItem::with(['department','docType'])->orderBy('name')->get();

        return view('admin.documents.edit', compact('document','departments','docTypes','items'));
    }

    /* =========================================================
     |  UPDATE
     |=========================================================*/
    public function update(Request $r, Document $document)
    {
        $data = $r->validate([
            'department_id' => 'nullable|exists:departments,id',
            'doc_type_id'   => 'nullable|exists:doc_types,id',
            'doc_item_id'   => 'nullable|exists:doc_items,id',
            'title'         => 'required|string|max:200',
            'slug'          => 'nullable|string|max:200|alpha_dash|unique:documents,slug,'.$document->id,
            'summary'       => 'nullable|string',
            'status'        => 'required|in:draft,open,archived',
            'published_at'  => 'nullable|date',
            'file'          => 'nullable|file|max:20480',
            'is_active'     => 'nullable|boolean',
        ]);

        $targetDept = (int)($data['department_id'] ?? $document->department_id);
        $targetType = (int)($data['doc_type_id']   ?? $document->doc_type_id);
        $targetItem = array_key_exists('doc_item_id',$data)
            ? ($data['doc_item_id'] ? (int)$data['doc_item_id'] : null)
            : ($document->doc_item_id ? (int)$document->doc_item_id : null);

        // Enforce EDIT terhadap target (kalau pindah departemen/jenis)
        abort_unless($r->user()->hasEditAccess($targetDept, $targetType, $targetItem), 403);

        if ($r->hasFile('file')) {
            Storage::disk('public')->delete($document->file_path);
            $path = $r->file('file')->store('documents/'.date('Y/m'),'public');
            $ext  = strtolower($r->file('file')->getClientOriginalExtension());
            $document->fill(['file_path'=>$path,'file_ext'=>$ext]);
        }

        $slug = !empty($data['slug']) ? Str::slug($data['slug']) : $document->slug;

        $document->fill([
            'department_id' => $targetDept,
            'doc_type_id'   => $targetType,
            'doc_item_id'   => $targetItem,
            'title'         => $data['title'],
            'slug'          => $slug,
            'summary'       => $data['summary'] ?? null,
            'status'        => $data['status'],
            'published_at'  => $data['published_at'] ?? $document->published_at ?? ($data['status']==='open' ? now() : null),
            'is_active'     => $r->boolean('is_active'),
        ])->save();

        return back()->with('ok','Dokumen diupdate.');
    }

    /* =========================================================
     |  DESTROY
     |=========================================================*/
    public function destroy(Request $request, Document $document)
    {
        abort_unless(
            $request->user()->hasEditAccess(
                (int)$document->department_id,
                (int)$document->doc_type_id,
                $document->doc_item_id ? (int)$document->doc_item_id : null
            ),
            403
        );

        Storage::disk('public')->delete($document->file_path);
        $document->delete();

        return back()->with('ok','Dokumen dihapus.');
    }
}
