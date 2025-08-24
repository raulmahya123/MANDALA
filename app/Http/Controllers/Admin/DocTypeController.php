<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
            'name' => ['required','string','max:100', Rule::unique('doc_types','name')],
        ]);

        DocType::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        return redirect()->route('admin.doc-types.index')->with('ok', 'Doc Type dibuat.');
    }

    public function edit(DocType $docType)
    {
        return view('admin.doc_types.edit', compact('docType'));
    }

    public function update(Request $request, DocType $docType)
    {
        $data = $request->validate([
            'name' => ['required','string','max:100', Rule::unique('doc_types','name')->ignore($docType->id)],
        ]);

        $docType->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        return redirect()->route('admin.doc-types.index')->with('ok', 'Doc Type diupdate.');
    }

    public function destroy(DocType $docType)
    {
        $docType->delete();
        return back()->with('ok', 'Doc Type dihapus.');
    }
}
