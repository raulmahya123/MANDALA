<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\DocType;
use App\Models\DocItem;
use App\Models\User;
use App\Models\DepartmentUserAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AccessController extends Controller
{
    /**
     * Tampilkan daftar akses user pada sebuah department.
     */
    public function index(Department $department)
    {
        $this->authorizeManage($department);

        $access = DepartmentUserAccess::with('user')
            ->where('department_id', $department->id)
            ->orderByDesc('id')
            ->paginate(25);

        $users    = User::orderBy('name')->get();
        $docTypes = DocType::orderBy('name')->get();
        $items    = DocItem::where('department_id', $department->id)->orderBy('name')->get();

        return view('admin.access.index', compact('department', 'access', 'users', 'docTypes', 'items'));
    }

    /**
     * Simpan akses baru untuk user pada department tertentu.
     */
    public function store(Request $request, Department $department)
    {
        $this->authorizeManage($department);

        // Validasi dasar
        $data = $request->validate([
            'user_id'    => ['required', 'exists:users,id'],
            'scope_type' => ['required', Rule::in(['department', 'doc_type', 'item'])],
            'scope_id'   => ['nullable', 'integer'],
            'can_edit'   => ['nullable', 'boolean'],
        ]);

        // Normalisasi boolean
        $data['can_edit'] = $request->boolean('can_edit');

        // Validasi & binding sesuai scope
        if ($data['scope_type'] === 'doc_type') {
            $request->validate([
                'scope_id' => ['required', 'exists:doc_types,id'],
            ]);
            // (opsional) kalau DocType punya hubungan ke Department, validasikan kepemilikan di sini.
            // Misal: jika DocType global, skip. Kalau punya kolom department_id, pastikan sama.
            // Contoh (aktifkan jika model DocType punya department_id):
            // $docType = DocType::findOrFail($data['scope_id']);
            // abort_unless($docType->department_id === $department->id, 422, 'Doc Type bukan milik Department ini.');
        } elseif ($data['scope_type'] === 'item') {
            $request->validate([
                'scope_id' => ['required', 'exists:doc_items,id'],
            ]);
            // DocItem harus milik department yang sama
            $item = DocItem::findOrFail($data['scope_id']);
            abort_unless($item->department_id === $department->id, 422, 'Item bukan milik Department ini.');
        } else {
            // scope department → scope_id null
            $data['scope_id'] = null;
        }

        // Cegah duplikasi akses (user_id + department_id + scope_type + scope_id unik)
        $request->validate([
            'user_id' => [
                Rule::unique('department_user_accesses', 'user_id')->where(function ($q) use ($department, $data) {
                    return $q->where('department_id', $department->id)
                             ->where('scope_type', $data['scope_type'])
                             ->whereNullWhen('scope_id', $data['scope_id'] === null)
                             ->when($data['scope_id'] !== null, fn ($qq) => $qq->where('scope_id', $data['scope_id']));
                }),
            ],
        ], [
            'user_id.unique' => 'Akses dengan kombinasi scope tersebut sudah ada untuk user ini.',
        ]);

        // Simpan
        DepartmentUserAccess::create([
            'user_id'       => $data['user_id'],
            'department_id' => $department->id,
            'scope_type'     => $data['scope_type'],
            'scope_id'       => $data['scope_id'],
            'can_edit'       => $data['can_edit'],
        ]);

        return back()->with('ok', 'Akses ditambahkan.');
    }

    /**
     * Hapus akses user pada department tertentu.
     */
    public function destroy(Department $department, DepartmentUserAccess $access)
    {
        $this->authorizeManage($department);

        // Pastikan akses yang dihapus memang milik department ini
        abort_unless($access->department_id === $department->id, 404);

        $access->delete();

        return back()->with('ok', 'Akses dihapus.');
    }

    /**
     * Otorisasi: super_admin lolos; admin harus admin di department ini.
     */
    private function authorizeManage(Department $department): void
    {
        /** @var \App\Models\User|null $u */
        $u = Auth::user();
        if (!$u) {
            abort(403);
        }

        if ($u->role === 'super_admin') {
            return;
        }

        // Pastikan relasi ini ada di User model:
        // public function adminDepartments(){ return $this->belongsToMany(Department::class, 'department_admins'); }
        if (!$u->adminDepartments()->whereKey($department->id)->exists()) {
            abort(403);
        }
    }
}
