<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocItem;
use App\Models\Department;
use App\Models\DocType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DocItemController extends Controller
{
    public function index()
    {
        $items = DocItem::with(['department','docType'])->paginate(20);
        return view('admin.doc_items.index', compact('items'));
    }

    public function create()
    {
        return view('admin.doc_items.create', [
            'departments'=>Department::all(),
            'docTypes'=>DocType::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'department_id'=>'required|exists:departments,id',
            'doc_type_id'=>'required|exists:doc_types,id',
            'name'=>'required|string|max:100',
        ]);
        DocItem::create([
            'department_id'=>$data['department_id'],
            'doc_type_id'=>$data['doc_type_id'],
            'name'=>$data['name'],
            'slug'=>Str::slug($data['name']),
        ]);
        return redirect()->route('admin.doc-items.index')->with('ok','Item dibuat.');
    }

    public function edit(DocItem $docItem)
    {
        return view('admin.doc_items.edit', compact('docItem'));
    }

    public function update(Request $request, DocItem $docItem)
    {
        $data = $request->validate(['name'=>'required|string|max:100']);
        $docItem->update([
            'name'=>$data['name'],
            'slug'=>Str::slug($data['name']),
        ]);
        return redirect()->route('admin.doc-items.index')->with('ok','Item diupdate.');
    }

    public function destroy(DocItem $docItem)
    {
        $docItem->delete();
        return back()->with('ok','Item dihapus.');
    }
}
