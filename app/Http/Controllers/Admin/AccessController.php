<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\DocType;
use App\Models\DocItem;
use App\Models\User;
use App\Models\DepartmentUserAccess;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    // daftar user & aksesnya pada suatu departemen
    public function index(Department $department)
    {
        $this->authorizeManage($department);

        $access = DepartmentUserAccess::with('user')
            ->where('department_id',$department->id)
            ->orderByDesc('id')
            ->paginate(25);

        $users = User::orderBy('name')->get();
        $docTypes = DocType::orderBy('name')->get();
        $items = DocItem::where('department_id',$department->id)->orderBy('name')->get();

        return view('admin.access.index', compact('department','access','users','docTypes','items'));
    }

    public function store(Request $r, Department $department)
    {
        $this->authorizeManage($department);

        $data = $r->validate([
            'user_id'     => 'required|exists:users,id',
            'scope_type'  => 'required|in:department,doc_type,item',
            'scope_id'    => 'nullable|integer',
            'can_edit'    => 'nullable|boolean',
        ]);

        // validasi scope_id sesuai scope_type
        if ($data['scope_type']==='doc_type') {
            $r->validate(['scope_id'=>'required|exists:doc_types,id']);
        } elseif ($data['scope_type']==='item') {
            $r->validate(['scope_id'=>'required|exists:doc_items,id']);
        } else {
            $data['scope_id'] = null;
        }

        DepartmentUserAccess::create([
            'user_id'=>$data['user_id'],
            'department_id'=>$department->id,
            'scope_type'=>$data['scope_type'],
            'scope_id'=>$data['scope_id'],
            'can_edit'=>$r->boolean('can_edit'),
        ]);

        return back()->with('ok','Akses ditambahkan.');
    }

    public function destroy(Department $department, DepartmentUserAccess $access)
    {
        $this->authorizeManage($department);
        abort_unless($access->department_id === $department->id, 404);

        $access->delete();
        return back()->with('ok','Akses dihapus.');
    }

    private function authorizeManage(Department $department): void
    {
        $u = auth()->user();
        if (!$u) abort(403);
        if ($u->role === 'super_admin') return;
        // hanya admin departemen pemilik yang boleh
        if (!$u->adminDepartments()->whereKey($department->id)->exists()) abort(403);
    }
}
