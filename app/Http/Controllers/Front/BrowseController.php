<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\DocType;
use App\Models\DocItem;
use App\Models\Document;
use Illuminate\Support\Facades\Storage;

class BrowseController extends Controller
{
    public function index()
    {
        $departments = Department::where('is_active', true)
            ->with(['docTypes' => fn($q) => $q->wherePivot('is_active', true)])
            ->orderBy('name')->get();
        return view('front.home', compact('departments'));
    }

    public function department(Department $department)
    {
        $docTypes = $department->docTypes()->wherePivot('is_active', true)->get();
        return view('front.department', compact('department', 'docTypes'));
    }

    public function listing(Department $department, DocType $docType)
    {
        $items = DocItem::where('department_id', $department->id)
            ->where('doc_type_id', $docType->id)->get();
        return view('front.list', compact('department', 'docType', 'items'));
    }

    public function item(Department $department, DocType $docType, DocItem $item)
    {
        $docs = Document::published()
            ->where('department_id', $department->id)
            ->where('doc_type_id', $docType->id)
            ->where('doc_item_id', $item->id)
            ->paginate(20);
        return view('front.item', compact('department', 'docType', 'item', 'docs'));
    }

    public function show(Document $document)
    {
        abort_unless($document->status === 'open', 404);
        return view('front.show', compact('document'));
    }

    public function download(Document $document)
    {
        abort_unless($document->status === 'open', 404);
        $path = Storage::disk('public')->path($document->file_path);
        return response()->download($path, $document->slug . '.' . $document->file_ext);
    }
}
