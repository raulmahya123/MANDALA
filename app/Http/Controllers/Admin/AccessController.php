<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\DepartmentUserAccess;
use App\Models\DocItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AccessController extends Controller
{
    /** Daftar akses untuk satu department */
    public function index(Department $department)
    {
        $this->authorizeManage($department);

        $accesses = DepartmentUserAccess::with('user')
            ->where('department_id', $department->id)
            ->orderByDesc('id')
            ->paginate(25);

        $users    = User::orderBy('name')->get(['id','name','email']);
        // ambil DocType yang terpasang di department ini
        $docTypes = $department->docTypes()->orderBy('name')->get(['id','name']);
        $items    = DocItem::where('department_id', $department->id)
                        ->with('docType:id,name')
                        ->orderBy('name')
                        ->get(['id','name','doc_type_id','department_id']);

        // VIEW YANG BENAR (sesuai folder kamu): resources/views/admin/departments/access.blade.php
        return view('admin.departments.access', compact('department','accesses','users','docTypes','items'));
    }

    /** Tambah grant akses */
    public function store(Request $request, Department $department)
    {
        $this->authorizeManage($department);

        $data = $request->validate([
            'user_id'    => ['required','exists:users,id'],
            'scope_type' => ['required', Rule::in(['department','doc_type','item'])],
            'scope_id'   => ['nullable','integer'],
            'can_edit'   => ['nullable','boolean'],
        ]);
        $data['can_edit'] = $request->boolean('can_edit');

        // Validasi sesuai scope
        if ($data['scope_type'] === 'doc_type') {
            $request->validate(['scope_id' => ['required','exists:doc_types,id']]);
        } elseif ($data['scope_type'] === 'item') {
            $request->validate(['scope_id' => ['required','exists:doc_items,id']]);
            $item = DocItem::findOrFail($data['scope_id']);
            abort_unless($item->department_id === $department->id, 422, 'Item bukan milik department ini.');
        } else {
            $data['scope_id'] = null; // scope department → scope_id null
        }

        // Cegah duplikasi kombinasi
        $exists = DepartmentUserAccess::where([
                'user_id'       => $data['user_id'],
                'department_id' => $department->id,
                'scope_type'    => $data['scope_type'],
            ])
            ->when($data['scope_id'] === null, fn($q) => $q->whereNull('scope_id'))
            ->when($data['scope_id'] !== null, fn($q) => $q->where('scope_id', $data['scope_id']))
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'user_id' => 'Akses dengan kombinasi scope tersebut sudah ada untuk user ini.',
            ])->withInput();
        }

        DepartmentUserAccess::create([
            'user_id'       => $data['user_id'],
            'department_id' => $department->id,
            'scope_type'    => $data['scope_type'],
            'scope_id'      => $data['scope_id'],
            'can_edit'      => $data['can_edit'],
        ]);

        return redirect()->route('admin.departments.access.index', $department)->with('ok','Akses ditambahkan.');
    }

    /** Hapus grant akses */
    public function destroy(Department $department, DepartmentUserAccess $access)
    {
        $this->authorizeManage($department);
        abort_unless($access->department_id === $department->id, 404);

        $access->delete();
        return back()->with('ok','Akses dihapus.');
    }

    /** super_admin bebas; selain itu harus admin di department ini (pivot department_user role=admin) */
    private function authorizeManage(Department $department): void
    {
        $u = Auth::user();
        abort_unless($u, 403);

        if (($u->role ?? null) === 'super_admin') return;

        $isAdminHere = $department->admins()->where('users.id', $u->id)->exists();
        abort_unless($isAdminHere, 403);
    }
}
