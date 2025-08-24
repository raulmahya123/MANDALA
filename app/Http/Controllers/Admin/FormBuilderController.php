<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormDefinition;
use App\Models\FormField;
use App\Models\Department;
use App\Models\DocType;
use App\Models\DocItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FormBuilderController extends Controller
{
    public function index(Request $r)
    {
        $u = $r->user();
        $q = FormDefinition::with(['department','docType','item']);

        if ($u->role !== 'super_admin') {
            $deptIds = $u->adminDepartments()->pluck('departments.id');
            $q->whereIn('department_id', $deptIds);
        }
        $forms = $q->latest()->paginate(20);
        return view('admin.forms.index', compact('forms'));
    }

    public function create(Request $r)
    {
        $u = $r->user();
        $departments = $u->role==='super_admin'
            ? Department::orderBy('name')->get()
            : Department::whereIn('id',$u->adminDepartments()->pluck('departments.id'))->orderBy('name')->get();
        $docTypes = DocType::orderBy('name')->get();
        $items = DocItem::orderBy('name')->get();

        return view('admin.forms.create', compact('departments','docTypes','items'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'department_id'=>'required|exists:departments,id',
            'doc_type_id'=>'required|exists:doc_types,id',
            'doc_item_id'=>'nullable|exists:doc_items,id',
            'title'=>'required|string|max:150',
            'is_active'=>'nullable|boolean',
        ]);
        // ACL edit
        abort_unless($r->user()->hasEditAccess((int)$data['department_id'], (int)$data['doc_type_id'], $data['doc_item_id']??null), 403);

        $form = FormDefinition::create([
            'department_id'=>$data['department_id'],
            'doc_type_id'=>$data['doc_type_id'],
            'doc_item_id'=>$data['doc_item_id'] ?? null,
            'title'=>$data['title'],
            'slug'=>Str::slug($data['title']).'-'.Str::random(5),
            'is_active'=>$r->boolean('is_active'),
        ]);
        return redirect()->route('admin.forms.edit',$form)->with('ok','Form dibuat. Tambahkan field.');
    }

    public function edit(FormDefinition $form)
    {
        return view('admin.forms.edit', [
            'form'=>$form->load('fields'),
        ]);
    }

    public function update(Request $r, FormDefinition $form)
    {
        $data = $r->validate([
            'title'=>'required|string|max:150',
            'is_active'=>'nullable|boolean',
        ]);
        // ACL: hanya pemilik dept atau SA
        abort_unless($r->user()->hasEditAccess($form->department_id, $form->doc_type_id, $form->doc_item_id), 403);

        $form->update([
            'title'=>$data['title'],
            'is_active'=>$r->boolean('is_active'),
        ]);
        return back()->with('ok','Form diupdate.');
    }

    // FIELD CRUD
    public function addField(Request $r, FormDefinition $form)
    {
        abort_unless($r->user()->hasEditAccess($form->department_id, $form->doc_type_id, $form->doc_item_id), 403);
        $data = $r->validate([
            'label'=>'required|string|max:120',
            'name'=>'required|string|max:120',
            'type'=>'required|in:text,textarea,number,date,select,checkbox',
            'options'=>'nullable|array', // untuk select/checkbox
            'required'=>'nullable|boolean',
            'sort_order'=>'nullable|integer',
        ]);
        FormField::create([
            'form_definition_id'=>$form->id,
            'label'=>$data['label'],
            'name'=>Str::slug($data['name'],'_'),
            'type'=>$data['type'],
            'options'=>$data['options'] ?? null,
            'required'=>$r->boolean('required'),
            'sort_order'=>$data['sort_order'] ?? 0,
        ]);
        return back()->with('ok','Field ditambahkan.');
    }

    public function deleteField(Request $r, FormDefinition $form, FormField $field)
    {
        abort_unless($field->form_definition_id === $form->id, 404);
        abort_unless($r->user()->hasEditAccess($form->department_id, $form->doc_type_id, $form->doc_item_id), 403);
        $field->delete();
        return back()->with('ok','Field dihapus.');
    }
}
