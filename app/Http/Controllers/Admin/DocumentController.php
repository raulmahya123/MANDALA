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
     * List dokumen (admin departemen melihat dokumen departemennya saja;
     * super admin melihat semua).
     */
    public function index(Request $request)
    {
        $u = $request->user();

        $query = Document::query()
            ->with(['department','docType','item'])
            ->latest('published_at');

        if ($u->role !== 'super_admin') {
            $deptIds = $u->adminDepartments()->pluck('departments.id');
            $query->whereIn('department_id', $deptIds);
        }

        // filter opsional
        if ($request->filled('q')) {
            $q = trim($request->string('q'));
            $query->where('title','like',"%{$q}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
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

        $departments = $u->role === 'super_admin'
            ? Department::orderBy('name')->get()
            : Department::whereIn('id', $u->adminDepartments()->pluck('departments.id'))
                ->orderBy('name')->get();

        $docTypes = DocType::orderBy('name')->get();

        // Item sebaiknya difilter via AJAX by dept+doctype, tetapi untuk starter kita tampilkan semua
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
            'summary'       => 'nullable|string',
            'file'          => 'required|file|max:20480', // 20MB
            'status'        => 'required|in:draft,open,archived',
            'published_at'  => 'nullable|date',
        ]);

        $deptId   = (int) $data['department_id'];
        $typeId   = (int) $data['doc_type_id'];
        $itemId   = $data['doc_item_id'] ? (int) $data['doc_item_id'] : null;

        // Enforce ACL edit
        abort_unless($r->user()->hasEditAccess($deptId, $typeId, $itemId), 403);

        // Simpan file
        $path = $r->file('file')->store('documents/'.date('Y/m'), 'public');
        $ext  = $r->file('file')->getClientOriginalExtension();

        $doc = Document::create([
            ...Arr::only($data, ['department_id','doc_type_id','doc_item_id','title','summary','status']),
            'slug'         => Str::slug($data['title']).'-'.Str::random(6),
            'file_path'    => $path,
            'file_ext'     => $ext,
            'published_at' => $data['published_at'] ?? now(),
            'uploaded_by'  => $r->user()->id,
        ]);

        return redirect()->route('admin.documents.index')->with('ok', 'Dokumen tersimpan.');
    }

    /**
     * Form edit: batasi daftar reference buat kenyamanan.
     */
    public function edit(Request $request, Document $document)
    {
        // Enforce ACL edit
        abort_unless(
            $request->user()->hasEditAccess(
                (int)$document->department_id,
                (int)$document->doc_type_id,
                $document->doc_item_id ? (int)$document->doc_item_id : null
            ),
            403
        );

        $u = $request->user();

        $departments = $u->role === 'super_admin'
            ? Department::orderBy('name')->get()
            : Department::whereIn('id', $u->adminDepartments()->pluck('departments.id'))
                ->orderBy('name')->get();

        $docTypes = DocType::orderBy('name')->get();
        $items    = DocItem::with(['department','docType'])->orderBy('name')->get();

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
        // Enforce ACL edit
        abort_unless(
            $r->user()->hasEditAccess(
                (int)$document->department_id,
                (int)$document->doc_type_id,
                $document->doc_item_id ? (int)$document->doc_item_id : null
            ),
            403
        );

        $data = $r->validate([
            'title'        => 'required|string|max:200',
            'summary'      => 'nullable|string',
            'status'       => 'required|in:draft,open,archived',
            'published_at' => 'nullable|date',
            'file'         => 'nullable|file|max:20480',
        ]);

        if ($r->hasFile('file')) {
            // Hapus file lama, simpan yang baru
            Storage::disk('public')->delete($document->file_path);
            $path = $r->file('file')->store('documents/'.date('Y/m'), 'public');
            $ext  = $r->file('file')->getClientOriginalExtension();
            $document->fill(['file_path' => $path, 'file_ext' => $ext]);
        }

        $document->fill([
            'title'        => $data['title'],
            'summary'      => $data['summary'] ?? null,
            'status'       => $data['status'],
            'published_at' => $data['published_at'] ?? $document->published_at ?? now(),
        ])->save();

        return back()->with('ok', 'Dokumen diupdate.');
    }

    /**
     * Hapus dokumen + file fisik.
     */
    public function destroy(Request $request, Document $document)
    {
        // Enforce ACL edit
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
