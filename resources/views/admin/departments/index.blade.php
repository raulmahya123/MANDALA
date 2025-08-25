@extends('layouts.admin')
@section('title','Departments')

@section('admin')
<div class="flex items-center justify-between mb-4">
  <form method="GET" class="flex gap-2">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama/slug..." class="rounded border px-3 py-2">
    <button class="px-3 py-2 rounded bg-slate-800 text-white">Cari</button>
  </form>
  <a href="{{ route('admin.departments.create') }}" class="px-3 py-2 rounded bg-emerald-600 text-white">+ Department</a>
</div>

@if(session('ok'))
  <div class="mb-3 p-3 rounded bg-emerald-50 border border-emerald-200 text-emerald-800">{{ session('ok') }}</div>
@endif

<div class="rounded-xl border bg-white overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-slate-50">
      <tr>
        <th class="text-left px-3 py-2">Nama</th>
        <th class="text-left px-3 py-2">Slug</th>
        <th class="text-left px-3 py-2">Active</th>
        <th class="text-left px-3 py-2">Docs</th>
        <th class="text-left px-3 py-2">Items</th>
        <th class="px-3 py-2">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($departments as $d)
      <tr class="border-t">
        <td class="px-3 py-2">{{ $d->name }}</td>
        <td class="px-3 py-2">{{ $d->slug }}</td>
        <td class="px-3 py-2">
          <span class="px-2 py-1 rounded text-xs {{ $d->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
            {{ $d->is_active ? 'Active' : 'Inactive' }}
          </span>
        </td>
        <td class="px-3 py-2">{{ $d->documents_count ?? 0 }}</td>
        <td class="px-3 py-2">{{ $d->items_count ?? 0 }}</td>
        <td class="px-3 py-2 text-right whitespace-nowrap">
          <a href="{{ route('admin.departments.edit',$d) }}" class="px-2 py-1 text-blue-700 hover:underline">Edit</a>
          <a href="{{ route('admin.departments.members',$d) }}" class="px-2 py-1 text-slate-700 hover:underline">Members</a>
          <a href="{{ route('admin.departments.docTypes',$d) }}" class="px-2 py-1 text-slate-700 hover:underline">Doc Types</a>
          <a href="{{ route('admin.departments.access',$d) }}" class="px-2 py-1 text-slate-700 hover:underline">Access</a>
          <form method="POST" action="{{ route('admin.departments.destroy',$d) }}" class="inline" onsubmit="return confirm('Hapus department ini?')">
            @csrf @method('DELETE')
            <button class="px-2 py-1 text-red-700 hover:underline">Hapus</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="6" class="px-3 py-6 text-center text-slate-500">Belum ada department.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $departments->links() }}</div>
@endsection
