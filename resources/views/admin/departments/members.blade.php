@extends('layouts.admin')
@section('title','Members — '.$department->name)

@section('admin')
@if(session('ok'))
  <div class="mb-3 p-3 rounded bg-emerald-50 border border-emerald-200 text-emerald-800">{{ session('ok') }}</div>
@endif
@if($errors->any())
  <div class="mb-3 p-3 rounded bg-red-50 border border-red-200 text-red-800">
    <ul class="list-disc ml-5">
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
  </div>
@endif

<h1 class="text-lg font-semibold mb-4">Members — {{ $department->name }}</h1>

<form method="POST" action="{{ route('admin.departments.members.store',$department) }}" class="rounded-xl border bg-white p-4 mb-6 grid md:grid-cols-3 gap-4">
  @csrf
  <div>
    <label class="block mb-1">User</label>
    <select name="user_id" class="w-full rounded border px-3 py-2" required>
      <option value="">Pilih...</option>
      @foreach($users as $u)
        <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label class="block mb-1">Role</label>
    <select name="role" class="w-full rounded border px-3 py-2" required>
      <option value="viewer">Viewer</option>
      <option value="contributor">Contributor</option>
      <option value="admin">Admin</option>
    </select>
  </div>
  <div class="flex items-end">
    <button class="px-3 py-2 rounded bg-emerald-600 text-white">Tambah/Update</button>
  </div>
</form>

<div class="rounded-xl border bg-white overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-slate-50">
      <tr>
        <th class="text-left px-3 py-2">User</th>
        <th class="text-left px-3 py-2">Email</th>
        <th class="text-left px-3 py-2">Role</th>
        <th class="px-3 py-2">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($members as $m)
      <tr class="border-t">
        <td class="px-3 py-2">{{ $m->name }}</td>
        <td class="px-3 py-2">{{ $m->email }}</td>
        <td class="px-3 py-2">
          <form method="POST" action="{{ route('admin.departments.members.update',[$department,$m]) }}" class="inline-flex gap-2 items-center">
            @csrf @method('PATCH')
            <select name="role" class="rounded border px-2 py-1">
              @foreach(['viewer','contributor','admin'] as $r)
                <option value="{{ $r }}" {{ $m->pivot->role===$r?'selected':'' }}>{{ ucfirst($r) }}</option>
              @endforeach
            </select>
            <button class="px-2 py-1 rounded bg-slate-800 text-white">Simpan</button>
          </form>
        </td>
        <td class="px-3 py-2 text-right">
          <form method="POST" action="{{ route('admin.departments.members.destroy',[$department,$m]) }}" onsubmit="return confirm('Hapus member ini?')" class="inline">
            @csrf @method('DELETE')
            <button class="px-2 py-1 text-red-700 hover:underline">Hapus</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="4" class="px-3 py-6 text-center text-slate-500">Belum ada member.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
