<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;


class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('name')->paginate(20);
        return view('admin.departments.index', compact('departments'));
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
        ]);

        Department::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        return redirect()->route('admin.departments.index')->with('ok','Departemen dibuat.');
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);

        $department->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.departments.index')->with('ok','Departemen diupdate.');
    }

    public function destroy(Department $department)
    {
        $department->delete();
        return back()->with('ok','Departemen dihapus.');
    }


public function addAdmin(Request $r, \App\Models\Department $department)
{
    // hanya super admin
    abort_unless($r->user()?->role === 'super_admin', 403);

    $data = $r->validate([
        'user_id' => ['required','exists:users,id'],
    ]);

    $department->admins()->syncWithoutDetaching([$data['user_id']]);

    return back()->with('ok','Admin departemen ditambahkan.');
}

public function removeAdmin(Request $r, \App\Models\Department $department, User $user)
{
    abort_unless($r->user()?->role === 'super_admin', 403);

    $department->admins()->detach($user->id);

    return back()->with('ok','Admin departemen dihapus.');
}

}
