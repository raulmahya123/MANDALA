<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormEntry;
use App\Models\FormApproval;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function index(Request $r)
    {
        $u = $r->user();
        // daftar yang perlu direview (yang status 'submitted' di dept yang dia pegang / SA semua)
        $q = FormEntry::with(['form.department','user'])->where('status','submitted');
        if ($u->role !== 'super_admin') {
            $deptIds = $u->adminDepartments()->pluck('departments.id');
            $q->whereHas('form', fn($f)=>$f->whereIn('department_id',$deptIds));
        }
        $entries = $q->latest('submitted_at')->paginate(20);
        return view('admin.approvals.index', compact('entries'));
    }

    public function decide(FormEntry $entry, Request $r)
    {
        $u = $r->user();
        $form = $entry->form;

        abort_unless($u->hasEditAccess($form->department_id, $form->doc_type_id, $form->doc_item_id), 403);

        $data = $r->validate([
            'action'=>'required|in:approved,rejected',
            'notes'=>'nullable|string',
        ]);

        $entry->status = $data['action'];
        $entry->approved_at = $data['action']==='approved' ? now() : null;
        $entry->rejected_at = $data['action']==='rejected' ? now() : null;
        $entry->save();

        FormApproval::create([
            'form_entry_id'=>$entry->id,
            'reviewer_id'=>$u->id,
            'action'=>$data['action'],
            'notes'=>$data['notes'] ?? null,
        ]);

        audit()->by($u)->on($entry)->action('form.'.$data['action'])->meta(['notes'=>$data['notes']??null])->log();
        notifySubmitter($entry, $data['action']); // helper notifikasi

        return back()->with('ok', 'Keputusan tersimpan.');
    }
}
