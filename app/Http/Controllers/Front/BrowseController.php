<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\DocType;
use App\Models\DocItem;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BrowseController extends Controller
{
    /**
     * Beranda: daftar departemen + jumlah doc type aktif.
     */
    public function index()
    {
        $departments = Department::where('is_active', true)
            ->with(['docTypes' => fn ($q) => $q->wherePivot('is_active', true)])
            ->orderBy('name')
            ->get();

        return view('front.home', compact('departments'));
    }

    /**
     * Halaman detail suatu departemen: tampil doc types aktif untuk departemen tsb.
     * Akses listing berikutnya akan diverifikasi per-user.
     */
    public function department(Department $department)
    {
        $docTypes = $department->docTypes()
            ->wherePivot('is_active', true)
            ->orderBy('pivot_sort_order')
            ->get();

        return view('front.department', compact('department', 'docTypes'));
    }

    /**
     * Halaman doc type dalam suatu departemen: tampil item (misal SOP → Mandi/Baca).
     * Enforce: user harus punya akses view setidaknya pada level department / doc_type.
     */
    public function listing(Request $request, Department $department, DocType $docType)
    {
        $user = $request->user();

        // Jika tidak login → tidak punya akses (ubah sesuai kebijakan jika ingin guest bisa akses)
        abort_unless($user && $user->hasViewAccess($department->id, $docType->id, null), 403);

        $items = DocItem::where('department_id', $department->id)
            ->where('doc_type_id', $docType->id)
            ->orderBy('name')
            ->get();

        return view('front.list', compact('department', 'docType', 'items'));
    }

    /**
     * Halaman daftar dokumen pada item tertentu.
     * Enforce view access pada level item.
     */
    public function item(Request $request, Department $department, DocType $docType, DocItem $item)
    {
        $user = $request->user();

        abort_unless($user && $user->hasViewAccess($department->id, $docType->id, $item->id), 403);

        $docs = Document::published()
            ->where('department_id', $department->id)
            ->where('doc_type_id', $docType->id)
            ->where('doc_item_id', $item->id)
            ->latest('published_at')
            ->paginate(20);

        return view('front.item', compact('department', 'docType', 'item', 'docs'));
    }

    /**
     * Detail dokumen spesifik (hanya status 'open').
     * Bisa ditambahkan pengecekan ACL, tapi biasanya jika user bisa mencapai halaman item,
     * dia sudah punya akses. Tetap kita cek ulang untuk aman.
     */
    public function show(Request $request, Document $document)
    {
        abort_unless($document->status === 'open', 404);

        $user = $request->user();
        abort_unless(
            $user && $user->hasViewAccess(
                (int)$document->department_id,
                (int)$document->doc_type_id,
                $document->doc_item_id ? (int)$document->doc_item_id : null
            ),
            403
        );

        return view('front.show', compact('document'));
    }

    /**
     * Download file dokumen (hanya 'open' & user punya akses).
     */
   public function download(Request $request, Document $document)
{
    abort_unless($document->status === 'open', 404);

    $user = $request->user();
    abort_unless(
        $user && $user->hasViewAccess(
            (int)$document->department_id,
            (int)$document->doc_type_id,
            $document->doc_item_id ? (int)$document->doc_item_id : null
        ),
        403
    );

    $path = Storage::disk('public')->path($document->file_path);
    return response()->download($path, $document->slug.'.'.$document->file_ext);
}
}
