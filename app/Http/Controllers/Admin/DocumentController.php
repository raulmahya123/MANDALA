<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Department;
use App\Models\DocType;
use App\Models\DocItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
class DocumentController extends Controller
{
    public function index()
    {
        $docs = Document::with(['department','docType','item'])->latest()->paginate(20);
        return view('admin.documents.index', compact('docs'));
    }

    public function create()
    {
        return view('admin.documents.create', [
            'departments'=>Department::all(),
            'docTypes'=>DocType::all(),
            'items'=>DocItem::all(),
        ]);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'department_id'=>'required|exists:departments,id',
            'doc_type_id'=>'required|exists:doc_types,id',
            'doc_item_id'=>'nullable|exists:doc_items,id',
            'title'=>'required|max:200',
            'summary'=>'nullable|string',
            'file'=>'required|file|max:20480',
            'status'=>'required|in:draft,open,archived',
            'published_at'=>'nullable|date'
        ]);

        $path = $r->file('file')->store('documents/'.date('Y/m'), 'public');
        $ext = $r->file('file')->getClientOriginalExtension();

        $doc = Document::create([
            ...Arr::only($data,['department_id','doc_type_id','doc_item_id','title','summary','status']),
            'slug'=>Str::slug($data['title']).'-'.Str::random(6),
            'file_path'=>$path,
            'file_ext'=>$ext,
            'published_at'=>$data['published_at'] ?? now(),
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->route('admin.documents.index')->with('ok','Dokumen tersimpan.');
    }

    public function edit(Document $document)
    {
        return view('admin.documents.edit', [
            'document'=>$document,
            'departments'=>Department::all(),
            'docTypes'=>DocType::all(),
            'items'=>DocItem::all(),
        ]);
    }

    public function update(Request $r, Document $document)
    {
        $data = $r->validate([
            'title'=>'required|max:200',
            'summary'=>'nullable|string',
            'status'=>'required|in:draft,open,archived',
            'published_at'=>'nullable|date',
            'file'=>'nullable|file|max:20480',
        ]);

        if ($r->hasFile('file')) {
            Storage::disk('public')->delete($document->file_path);
            $path = $r->file('file')->store('documents/'.date('Y/m'), 'public');
            $ext = $r->file('file')->getClientOriginalExtension();
            $document->update(['file_path'=>$path,'file_ext'=>$ext]);
        }

        $document->update([
            'title'=>$data['title'],
            'summary'=>$data['summary'] ?? null,
            'status'=>$data['status'],
            'published_at'=>$data['published_at'] ?? now(),
        ]);

        return back()->with('ok','Dokumen diupdate.');
    }

    public function destroy(Document $document)
    {
        Storage::disk('public')->delete($document->file_path);
        $document->delete();
        return back()->with('ok','Dokumen dihapus.');
    }
}
