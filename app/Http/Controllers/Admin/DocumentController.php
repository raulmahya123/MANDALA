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
    /**
     * Ambil daftar ID department yang boleh dikelola user (admin dept + ACL can_edit).
     */
    private function allowedDepartmentIds($user)
    {
        if ($user->role === 'super_admin') {
            // semua dept
            return Department::pluck('id');
        }

        return $user->adminDepartments()->pluck('departments.id')
            ->merge($user->acl()->where('can_edit', true)->pluck('department_id'))
            ->unique()->values();
    }

    /**
     * Ambil daftar model Department untuk form create/edit.
     */
    private function allowedDepartmentsForEditor($user)
    {
        if ($user->role === 'super_admin') {
            return Department::orderBy('name')->get();
        }

        $ids = $this->allowedDepartmentIds($user);
        return Department::whereIn('id', $ids)->orderBy('name')->get();
    }

    /**
     * List dokumen (admin departemen melihat dokumen departemennya saja; super admin melihat semua).
     */
    public function index(Request $request)
    {
        $u = $request->user();

        $query = Document::query()
            ->with(['department','docType','item'])
            ->latest('published_at');

        if ($u->role !== 'super_admin') {
            $deptIds = $this->allowedDepartmentIds($u);
            $query->whereIn('department_id', $deptIds);
        }

        // filter opsional
        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            if ($q !== '') {
                $query->where('title','like',"%{$q}%");
            }
        }
        if ($request->filled('status')) {
            $query->where('status', (string) $request->input('status'));
        }

        $docs = $query->paginate(20)->withQueryString();

        return view('admin.documents.index', compact('docs'));
    }

    /**
     * Form create: batasi pilihan department untuk admin departemen.
     */
    public function create(Request $request)
    {
        $u = $request->user();

        $departments = $this->allowedDepartmentsForEditor($u);
        $docTypes    = DocType::orderBy('name')->get();

        // Item sebaiknya difilter via AJAX by dept+doctype, untuk starter tampilkan semua
        $items = DocItem::with(['department','docType'])->orderBy('name')->get();

        return view('admin.documents.create', compact('departments','docTypes','items'));
    }

    /**
     * Simpan dokumen baru (cek ACL edit).
     */
    public function store(Request $r)
    {
        $data = $r->validate([
            'department_id' => 'required|exists:departments,id',
            'doc_type_id'   => 'required|exists:doc_types,id',
            'doc_item_id'   => 'nullable|exists:doc_items,id',
            'title'         => 'required|string|max:200',
            'slug'          => 'nullable|string|max:200|alpha_dash|unique:documents,slug',
            'summary'       => 'nullable|string',
            'file'          => 'required|file|max:20480', // 20MB
            'status'        => 'required|in:draft,open,archived',
            'published_at'  => 'nullable|date',
            'is_active'     => 'nullable|boolean',
        ]);

        $deptId = (int) $data['department_id'];
        $typeId = (int) $data['doc_type_id'];
        $itemId = isset($data['doc_item_id']) ? (int) $data['doc_item_id'] : null;

        // Enforce ACL edit pada target
        abort_unless($r->user()->hasEditAccess($deptId, $typeId, $itemId), 403);

        // Simpan file
        $path = $r->file('file')->store('documents/'.date('Y/m'), 'public');
        $ext  = strtolower($r->file('file')->getClientOriginalExtension());

        // Slug: pakai input kalau ada, kalau tidak generate dari title + random
        $slug = !empty($data['slug'])
            ? Str::slug($data['slug'])
            : Str::slug($data['title']).'-'.Str::random(6);

        $doc = Document::create([
            ...Arr::only($data, ['department_id','doc_type_id','doc_item_id','title','summary','status']),
            'slug'         => $slug,
            'file_path'    => $path,
            'file_ext'     => $ext,
            'published_at' => $data['published_at'] ?? ($data['status'] === 'open' ? now() : null),
            'uploaded_by'  => $r->user()->id,
            'is_active'    => $r->boolean('is_active'),
        ]);

        return redirect()->route('admin.documents.index')->with('ok', 'Dokumen tersimpan.');
    }

    /**
     * Form edit: batasi daftar reference buat kenyamanan.
     */
    public function edit(Request $request, Document $document)
    {
        // Enforce ACL edit pada dokumen saat ini
        abort_unless(
            $request->user()->hasEditAccess(
                (int)$document->department_id,
                (int)$document->doc_type_id,
                $document->doc_item_id ? (int)$document->doc_item_id : null
            ),
            403
        );

        $u = $request->user();

        $departments = $this->allowedDepartmentsForEditor($u);
        $docTypes    = DocType::orderBy('name')->get();
        $items       = DocItem::with(['department','docType'])->orderBy('name')->get();

        return view('admin.documents.edit', [
            'document'    => $document,
            'departments' => $departments,
            'docTypes'    => $docTypes,
            'items'       => $items,
        ]);
    }

    /**
     * Update dokumen (ganti metadata / file).
     */
    public function update(Request $r, Document $document)
    {
        $data = $r->validate([
            'department_id' => 'nullable|exists:departments,id',
            'doc_type_id'   => 'nullable|exists:doc_types,id',
            'doc_item_id'   => 'nullable|exists:doc_items,id',
            'title'         => 'required|string|max:200',
            'slug'          => 'nullable|string|max:200|alpha_dash|unique:documents,slug,' . $document->id,
            'summary'       => 'nullable|string',
            'status'        => 'required|in:draft,open,archived',
            'published_at'  => 'nullable|date',
            'file'          => 'nullable|file|max:20480',
            'is_active'     => 'nullable|boolean',
        ]);

        // Target scope (kalau tidak diisi, pakai yang lama)
        $targetDept = (int)($data['department_id'] ?? $document->department_id);
        $targetType = (int)($data['doc_type_id']   ?? $document->doc_type_id);
        $targetItem = array_key_exists('doc_item_id', $data)
            ? ($data['doc_item_id'] ? (int)$data['doc_item_id'] : null)
            : ($document->doc_item_id ? (int)$document->doc_item_id : null);

        // Enforce ACL edit pada target (agar aman bila dipindahkan)
        abort_unless($r->user()->hasEditAccess($targetDept, $targetType, $targetItem), 403);

        // Handle file baru
        if ($r->hasFile('file')) {
            Storage::disk('public')->delete($document->file_path);
            $path = $r->file('file')->store('documents/'.date('Y/m'), 'public');
            $ext  = strtolower($r->file('file')->getClientOriginalExtension());
            $document->fill(['file_path' => $path, 'file_ext' => $ext]);
        }

        // Slug: pakai input kalau ada; jika kosong, biarkan slug lama
        $slug = !empty($data['slug']) ? Str::slug($data['slug']) : $document->slug;

        $document->fill([
            'department_id' => $targetDept,
            'doc_type_id'   => $targetType,
            'doc_item_id'   => $targetItem,
            'title'         => $data['title'],
            'slug'          => $slug,
            'summary'       => $data['summary'] ?? null,
            'status'        => $data['status'],
            'published_at'  => $data['published_at'] ?? $document->published_at ?? ($data['status'] === 'open' ? now() : null),
            'is_active'     => $r->boolean('is_active'),
        ])->save();

        return back()->with('ok', 'Dokumen diupdate.');
    }

    /**
     * Hapus dokumen + file fisik.
     */
    public function destroy(Request $request, Document $document)
    {
        // Enforce ACL edit pada dokumen saat ini
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

        return back()->with('ok', 'Dokumen dihapus.');
    }
}
