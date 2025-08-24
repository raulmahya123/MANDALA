<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class DocTypeController extends Controller
{
    public function index()
    {
        $docTypes = DocType::orderBy('name')->paginate(20);
        return view('admin.doc_types.index', compact('docTypes'));
    }

    public function create()
    {
        return view('admin.doc_types.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100','unique:doc_types,name'],
            'slug' => ['nullable','string','max:120', 'regex:/^[A-Za-z0-9\-\_]+$/', 'unique:doc_types,slug'],
        ]);

        // Jika slug kosong → turunkan dari name
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        } else {
            // Paksa slugify biar konsisten
            $data['slug'] = Str::slug($data['slug']);
        }

        DocType::create($data);

        return redirect()->route('admin.doc-types.index')->with('ok','Doc Type dibuat.');
    }

    public function edit(DocType $docType)
    {
        return view('admin.doc_types.edit', compact('docType'));
    }

    public function update(Request $request, DocType $docType)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100', Rule::unique('doc_types','name')->ignore($docType->id)],
            'slug' => ['nullable','string','max:120', 'regex:/^[A-Za-z0-9\-\_]+$/', Rule::unique('doc_types','slug')->ignore($docType->id)],
        ]);

        // Jika slug kosong → turunkan dari name, else slugify input
        $data['slug'] = empty($data['slug'])
            ? Str::slug($data['name'])
            : Str::slug($data['slug']);

        $docType->update($data);

        return redirect()->route('admin.doc-types.index')->with('ok','Doc Type diupdate.');
    }

    public function destroy(DocType $docType)
    {
        $docType->delete();
        return back()->with('ok','Doc Type dihapus.');
    }
}
