<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use App\Models\DocType;
use App\Models\DocItem;
use App\Models\DepartmentUserAccess;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    // =========================
    // CRUD Department
    // =========================
    public function index(Request $r)
    {
        $q = Department::query()->withCount(['documents','items']);
        if ($s = $r->get('q')) {
            $q->where(function($qq) use ($s){
                $qq->where('name','like',"%{$s}%")->orWhere('slug','like',"%{$s}%");
            });
        }
        $departments = $q->orderBy('name')->paginate(20)->withQueryString();
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name' => ['required','string','max:190'],
            'slug' => ['nullable','string','max:190', 'unique:departments,slug'],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? true);
        $dept = Department::create($data);
        return redirect()->route('admin.departments.edit',$dept)->with('ok','Department dibuat.');
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $r, Department $department)
    {
        $data = $r->validate([
            'name' => ['required','string','max:190'],
            'slug' => ['nullable','string','max:190', Rule::unique('departments','slug')->ignore($department->id)],
            'is_active' => ['nullable','boolean'],
        ]);
        $data['is_active'] = (bool)($data['is_active'] ?? false);
        $department->update($data);
        return back()->with('ok','Department diupdate.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('admin.departments.index')->with('ok','Department dihapus.');
    }

    // =========================
    // Members (pivot department_user : role)
    // =========================
    public function members(Department $department)
    {
        $members = $department->users()->withPivot('role','created_at')->orderBy('name')->get();
        $users = User::orderBy('name')->get();
        return view('admin.departments.members', compact('department','members','users'));
    }

    public function membersStore(Request $r, Department $department)
    {
        $data = $r->validate([
            'user_id' => ['required','exists:users,id'],
            'role'    => ['required', Rule::in(['admin','contributor','viewer'])],
        ]);

        // attach or update role
        $department->users()->syncWithoutDetaching([
            $data['user_id'] => ['role' => $data['role']]
        ]);

        return back()->with('ok','Member ditambahkan/diperbarui.');
    }

    public function membersUpdate(Request $r, Department $department, User $user)
    {
        $data = $r->validate([
            'role' => ['required', Rule::in(['admin','contributor','viewer'])],
        ]);

        $department->users()->updateExistingPivot($user->id, ['role'=>$data['role']]);
        return back()->with('ok','Role diperbarui.');
    }

    public function membersDestroy(Department $department, User $user)
    {
        $department->users()->detach($user->id);
        return back()->with('ok','Member dihapus dari department.');
    }

    // =========================
    // Doc Types dalam Department (pivot department_doc_type)
    // =========================
    public function docTypes(Department $department)
    {
        $attached = $department->docTypes()->withPivot(['is_active','sort_order'])->orderBy('pivot_sort_order')->get();
        $available = DocType::whereNotIn('id', $attached->pluck('id'))->orderBy('name')->get();

        return view('admin.departments.doc-types', compact('department','attached','available'));
    }

    public function docTypesAttach(Request $r, Department $department)
    {
        $data = $r->validate([
            'doc_type_id' => ['required','exists:doc_types,id'],
            'is_active'   => ['nullable','boolean'],
            'sort_order'  => ['nullable','integer','min:0'],
        ]);

        $department->docTypes()->syncWithoutDetaching([
            $data['doc_type_id'] => [
                'is_active'  => (bool)($data['is_active'] ?? true),
                'sort_order' => (int)($data['sort_order'] ?? 0),
            ]
        ]);

        return back()->with('ok','Doc Type ditautkan.');
    }

    public function docTypesUpdate(Request $r, Department $department, DocType $docType)
    {
        $data = $r->validate([
            'is_active'  => ['required','boolean'],
            'sort_order' => ['required','integer','min:0'],
        ]);

        $department->docTypes()->updateExistingPivot($docType->id, [
            'is_active'  => (bool)$data['is_active'],
            'sort_order' => (int)$data['sort_order'],
        ]);

        return back()->with('ok','Pengaturan Doc Type diperbarui.');
    }

    public function docTypesDetach(Department $department, DocType $docType)
    {
        $department->docTypes()->detach($docType->id);
        return back()->with('ok','Doc Type dilepas dari department.');
    }

    // =========================
    // Akses granular (department_user_access)
    // =========================
    public function access(Department $department)
    {
        $accesses = DepartmentUserAccess::with(['user'])
            ->where('department_id',$department->id)
            ->latest()->get();

        $users = User::orderBy('name')->get();
        $docTypes = $department->docTypes()->orderBy('name')->get();
        $items = DocItem::where('department_id',$department->id)->orderBy('name')->get();

        return view('admin.departments.access', compact('department','accesses','users','docTypes','items'));
    }

    public function accessStore(Request $r, Department $department)
    {
        $data = $r->validate([
            'user_id'    => ['required','exists:users,id'],
            'scope_type' => ['required', Rule::in(['department','doc_type','item'])],
            'scope_id'   => ['nullable','integer'],
            'can_edit'   => ['nullable','boolean'],
        ]);

        // Validasi scope_id sesuai scope_type (opsional lebih ketat)
        if ($data['scope_type'] !== 'department' && !$data['scope_id']) {
            return back()->withErrors(['scope_id'=>'scope_id wajib untuk scope selain department']);
        }

        DepartmentUserAccess::create([
            'user_id' => $data['user_id'],
            'department_id' => $department->id,
            'scope_type' => $data['scope_type'],
            'scope_id' => $data['scope_type']==='department' ? null : $data['scope_id'],
            'can_edit' => (bool)($data['can_edit'] ?? false),
        ]);

        return back()->with('ok','Akses ditambahkan.');
    }

    public function accessDestroy(Department $department, DepartmentUserAccess $access)
    {
        // jaga-jaga: pastikan milik dept ini
        abort_if($access->department_id !== $department->id, 404);
        $access->delete();
        return back()->with('ok','Akses dihapus.');
    }
}
