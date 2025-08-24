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
    /**
     * List semua form aktif yang bisa diakses user.
     * Route: GET /forms  (name: forms.index)
     */
    public function index(Request $r)
    {
        $user = $r->user();

        // Ambil form aktif + relasi dasar
        $forms = FormDefinition::with(['department','docType','item'])
            ->where('is_active', true)
            ->orderBy('title')
            ->get()
            // filter sesuai akses user
            ->filter(fn ($f) => $user->hasViewAccess($f->department_id, $f->doc_type_id, $f->doc_item_id))
            ->values();

        return view('front.forms.index', compact('forms'));
    }

    /**
     * Tampilkan halaman isi form.
     * Route: GET /form/{form:slug}  (name: form.fill)
     */
    public function show(FormDefinition $form, Request $r)
    {
        $user = $r->user();
        abort_unless($user && $user->hasViewAccess($form->department_id, $form->doc_type_id, $form->doc_item_id), 403);
        abort_unless($form->is_active, 404);

        $form->load('fields');
        return view('front.forms.fill', compact('form'));
    }

    /**
     * Simpan entri form (draft atau submit).
     * Route: POST /form/{form:slug}  (name: form.submit)
     */
    public function store(FormDefinition $form, Request $r)
    {
        $user = $r->user();
        abort_unless($user && $user->hasViewAccess($form->department_id, $form->doc_type_id, $form->doc_item_id), 403);

        // Validasi dinamis untuk field yang required
        $form->load('fields');
        $rules = [];
        foreach ($form->fields as $f) {
            if ($f->required) {
                // tipe sederhana – silakan perluas sesuai kebutuhan
                $rules[$f->name] = 'required';
            }
        }
        $validated = $r->validate($rules);

        DB::transaction(function () use ($form, $user, $r) {

            $entry = FormEntry::create([
                'form_definition_id' => $form->id,
                'user_id'            => $user->id,
                'status'             => $r->has('submit') ? 'submitted' : 'draft',
                'submitted_at'       => $r->has('submit') ? now() : null,
            ]);

            // Simpan nilai tiap field
            foreach ($form->fields as $f) {
                $name = $f->name;
                $val  = $r->input($name);

                // Checkbox bisa multiple → simpan sebagai JSON array
                if ($f->type === 'checkbox') {
                    if (is_array($val)) {
                        $val = json_encode(array_values($val));
                    } elseif (is_null($val)) {
                        // kalau unchecked dan tidak required, simpan kosong
                        $val = json_encode([]);
                    }
                }

                FormEntryValue::create([
                    'form_entry_id' => $entry->id,
                    'form_field_id' => $f->id,
                    'value'         => $val,
                ]);
            }

            // Jika disubmit, catat trail approval awal + audit/notify jika tersedia
            if ($entry->status === 'submitted') {
                FormApproval::create([
                    'form_entry_id' => $entry->id,
                    'reviewer_id'   => $user->id,
                    'action'        => 'submitted',
                    'notes'         => null,
                ]);

                // Opsional: hanya jalan jika helper tersedia
                if (function_exists('audit')) {
                    audit()->by($user)->on($entry)->action('form.submit')->meta(['ip' => $r->ip()])->log();
                }
                if (function_exists('notifyApprovers')) {
                    notifyApprovers($form, $entry);
                }
            }
        });

        return redirect()
            ->route('home')
            ->with('ok', $r->has('submit') ? 'Form dikirim untuk ditinjau.' : 'Draft disimpan.');
    }

    /**
     * Lihat detail satu entry milik user (atau admin dept/SA).
     * Route: GET /entry/{entry}  (name: form.entry.show)
     */
    public function showEntry(FormEntry $entry, Request $r)
    {
        $user = $r->user();
        $form = $entry->form()->with('fields')->firstOrFail();

        // Submitter sendiri ATAU admin dept pemilik form ATAU super_admin
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
}
