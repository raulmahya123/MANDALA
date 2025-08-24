<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocItem;
use App\Models\Department;
use App\Models\DocType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class DocItemController extends Controller
{
    public function index()
    {
        $items = DocItem::with(['department','docType'])
            ->latest()
            ->paginate(20);

        // resources/views/admin/doc_items/index.blade.php
        return view('admin.doc_items.index', compact('items'));
    }

    public function create()
    {
        $departments = Department::orderBy('name')->get();
        $docTypes    = DocType::orderBy('name')->get();

        // resources/views/admin/doc_items/create.blade.php
        return view('admin.doc_items.create', compact('departments','docTypes'));
    }

    public function store(Request $request)
    {
        // Validasi dasar
        $data = $request->validate([
            'department_id' => ['required','exists:departments,id'],
            'doc_type_id'   => ['required','exists:doc_types,id'],
            'name'          => ['required','string','max:100'],
        ]);

        // Siapkan slug utk validasi unik
        $slug = Str::slug($data['name']);
        $request->merge(['slug' => $slug]);

        // Unik per (department_id, doc_type_id, slug)
        $request->validate([
            'slug' => [
                'required',
                Rule::unique('doc_items','slug')->where(fn($q) =>
                    $q->where('department_id', $data['department_id'])
                      ->where('doc_type_id', $data['doc_type_id'])
                ),
            ],
        ]);

        DocItem::create([
            'department_id' => $data['department_id'],
            'doc_type_id'   => $data['doc_type_id'],
            'name'          => $data['name'],
            'slug'          => $slug,     // model juga auto-set, tapi eksplisit OK
            'is_active'     => true,
        ]);

        return redirect()->route('admin.doc-items.index')->with('status','Doc Item berhasil dibuat.');
    }

    public function edit(DocItem $docItem)
    {
        $departments = Department::orderBy('name')->get();
        $docTypes    = DocType::orderBy('name')->get();

        // resources/views/admin/doc_items/edit.blade.php
        return view('admin.doc_items.edit', compact('docItem','departments','docTypes'));
    }

    public function update(Request $request, DocItem $docItem)
    {
        // Validasi dasar
        $data = $request->validate([
            'department_id' => ['required','exists:departments,id'],
            'doc_type_id'   => ['required','exists:doc_types,id'],
            'name'          => ['required','string','max:100'],
        ]);

        $slug = Str::slug($data['name']);
        $request->merge(['slug' => $slug]);

        // Unik per (department_id, doc_type_id, slug) kecuali dirinya
        $request->validate([
            'slug' => [
                'required',
                Rule::unique('doc_items','slug')
                    ->ignore($docItem->id)
                    ->where(fn($q) =>
                        $q->where('department_id', $data['department_id'])
                          ->where('doc_type_id', $data['doc_type_id'])
                    ),
            ],
        ]);

        $docItem->update([
            'department_id' => $data['department_id'],
            'doc_type_id'   => $data['doc_type_id'],
            'name'          => $data['name'],
            'slug'          => $slug, // model auto-set juga
        ]);

        return redirect()->route('admin.doc-items.index')->with('status','Doc Item diperbarui.');
    }

    public function destroy(DocItem $docItem)
    {
        $docItem->delete();
        return back()->with('status','Doc Item dihapus.');
    }
}
