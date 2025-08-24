<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\FormDefinition;
use App\Models\FormEntry;
use App\Models\FormEntryValue;
use App\Models\FormApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormEntryController extends Controller
{
    // tampilkan form untuk diisi
    public function show(FormDefinition $form, Request $r)
    {
        $user = $r->user();
        abort_unless($user && $user->hasViewAccess($form->department_id, $form->doc_type_id, $form->doc_item_id), 403);
        abort_unless($form->is_active, 404);

        $form->load('fields');
        return view('front.forms.fill', compact('form'));
    }

    // simpan entry (draft atau submit)
    public function store(FormDefinition $form, Request $r)
    {
        $user = $r->user();
        abort_unless($user && $user->hasViewAccess($form->department_id, $form->doc_type_id, $form->doc_item_id), 403);

        $payload = $r->all();

        DB::transaction(function() use ($form,$user,$payload,$r) {
            $entry = FormEntry::create([
                'form_definition_id'=>$form->id,
                'user_id'=>$user->id,
                'status'=> $r->has('submit') ? 'submitted' : 'draft',
                'submitted_at'=> $r->has('submit') ? now() : null,
            ]);

            foreach ($form->fields as $f) {
                $val = $payload[$f->name] ?? null;
                if ($f->type==='checkbox' && is_array($val)) {
                    $val = json_encode(array_values($val));
                }
                FormEntryValue::create([
                    'form_entry_id'=>$entry->id,
                    'form_field_id'=>$f->id,
                    'value'=>$val,
                ]);
            }

            // audit + approval trail (submitted)
            if ($entry->status==='submitted') {
                FormApproval::create([
                    'form_entry_id'=>$entry->id,
                    'reviewer_id'=>$user->id,
                    'action'=>'submitted',
                    'notes'=>null,
                ]);
                audit()->by($user)->on($entry)->action('form.submit')->meta(['ip'=>$r->ip()])->log();
                notifyApprovers($form, $entry); // helper notifikasi (lihat bagian Notifikasi)
            }
        });

        return redirect()->route('home')->with('ok', $r->has('submit') ? 'Form dikirim untuk ditinjau.' : 'Draft disimpan.');
    }

    // lihat detail entry (punya sendiri atau admin dept)
    public function showEntry(FormEntry $entry, Request $r)
    {
        $user = $r->user();
        $form = $entry->form()->with('fields')->first();

        // boleh jika submitter sendiri atau admin dept pemilik atau SA
        abort_unless(
            $user && ($entry->user_id === $user->id || $user->hasEditAccess($form->department_id, $form->doc_type_id, $form->doc_item_id)),
            403
        );

        $entry->load(['values.field','approvals']);
        return view('front.forms.entry', compact('form','entry'));
    }
}
