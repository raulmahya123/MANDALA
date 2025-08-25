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
    // GET /forms  name: forms.index
    public function index(Request $r)
    {
        $user = $r->user();

        $forms = FormDefinition::with(['department', 'docType', 'item'])
            ->where('is_active', true)
            ->orderBy('title')
            ->get()
            ->filter(fn($f) => $user->hasViewAccess($f->department_id, $f->doc_type_id, $f->doc_item_id))
            ->values();

        return view('front.forms.index', compact('forms'));
    }

    // GET /form/{form:slug}  name: form.fill
    public function show(FormDefinition $form, Request $r)
    {
        $user = $r->user();

        abort_unless($user && $user->hasViewAccess($form->department_id, $form->doc_type_id, $form->doc_item_id), 403);
        abort_unless($form->is_active, 404);

        $form->load('fields');
        return view('front.forms.fill', compact('form'));
    }

    // POST /form/{form:slug}  name: form.submit
    public function store(FormDefinition $form, Request $r)
    {
        $user = $r->user();
        abort_unless($user && $user->hasViewAccess($form->department_id, $form->doc_type_id, $form->doc_item_id), 403);

        $form->load('fields');

        // Validasi dinamis
        $rules = [];
        foreach ($form->fields as $f) {
            $rule = [];
            if ($f->required) $rule[] = 'required';

            // contoh rule ekstra per tipe
            switch ($f->type) {
                case 'number':
                    $rule[] = 'numeric';
                    break;
                case 'email':
                    $rule[] = 'email';
                    break;
                case 'date':
                    $rule[] = 'date';
                    break;
                case 'file':
                    $rule[] = 'file';
                    break;
                default:         /* text/textarea/radio/checkbox */
                    break;
            }

            if ($rule) $rules[$f->name] = implode('|', $rule);
        }
        $validated = $r->validate($rules);

        $entryId = DB::transaction(function () use ($form, $user, $r) {
            $entry = FormEntry::create([
                'form_definition_id' => $form->id,
                'user_id'            => $user->id,
                'status'             => $r->has('submit') ? 'submitted' : 'draft',
                'submitted_at'       => $r->has('submit') ? now() : null,
            ]);

            foreach ($form->fields as $f) {
                $name = $f->name;
                $val  = $r->input($name);

                if ($f->type === 'checkbox') {
                    $val = is_array($val) ? json_encode(array_values($val)) : json_encode([]);
                }

                FormEntryValue::create([
                    'form_entry_id' => $entry->id,
                    'form_field_id' => $f->id,
                    'value'         => $val,
                ]);
            }

            if ($entry->status === 'submitted') {
                FormApproval::create([
                    'form_entry_id' => $entry->id,
                    'reviewer_id'   => $user->id,  // pencatat aksi (submitter)
                    'action'        => 'submitted',
                    'notes'         => null,
                ]);

                if (function_exists('audit')) {
                    audit()->by($user)->on($entry)->action('form.submit')
                        ->meta(['ip' => $r->ip()])->log();
                }
                if (function_exists('notifyApprovers')) {
                    notifyApprovers($form, $entry);
                }
            }

            return $entry->id;
        });

        return redirect()
            ->route('home') // atau ->route('form.entry.show', $entryId)
            ->with('ok', $r->has('submit') ? 'Form dikirim untuk ditinjau.' : 'Draft disimpan.');
    }

    // GET /entry/{entry}  name: form.entry.show
    public function showEntry(FormEntry $entry, Request $r)
    {
        $user = $r->user();
        $form = $entry->form()->with('fields')->firstOrFail();

        abort_unless(
            $user && (
                $entry->user_id === $user->id
                || $user->hasEditAccess($form->department_id, $form->doc_type_id, $form->doc_item_id)
            ),
            403
        );

        $entry->load(['values.field', 'approvals']);

        return view('front.forms.entry', compact('form', 'entry'));
    }
    public function myEntries(Request $r)
    {
        $entries = \App\Models\FormEntry::with('form:id,slug,title')
            ->where('user_id', $r->user()->id)
            ->latest('created_at')
            ->paginate(15);

        return view('front.forms.my-entries', compact('entries'));
    }
}
