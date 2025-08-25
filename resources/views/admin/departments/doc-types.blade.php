@extends('layouts.admin')
@section('title','Doc Types — '.$department->name)

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

<h1 class="text-lg font-semibold mb-4">Doc Types — {{ $department->name }}</h1>

<form method="POST" action="{{ route('admin.departments.docTypes.attach',$department) }}" class="rounded-xl border bg-white p-4 mb-6 grid md:grid-cols-4 gap-4">
  @csrf
  <div>
    <label class="block mb-1">Doc Type</label>
    <select name="doc_type_id" class="w-full rounded border px-3 py-2" required>
      <option value="">Pilih...</option>
      @foreach($available as $dt)
        <option value="{{ $dt->id }}">{{ $dt->name }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label class="block mb-1">Sort Order</label>
    <input type="number" name="sort_order" value="0" class="w-full rounded border px-3 py-2" min="0">
  </div>
  <div class="flex items-center gap-2">
    <input type="checkbox" name="is_active" value="1" id="is_active" checked>
    <label for="is_active">Active</label>
  </div>
  <div class="flex items-end">
    <button class="px-3 py-2 rounded bg-emerald-600 text-white">Attach</button>
  </div>
</form>

<div class="rounded-xl border bg-white overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-slate-50">
      <tr>
        <th class="text-left px-3 py-2">Nama</th>
        <th class="text-left px-3 py-2">Active</th>
        <th class="text-left px-3 py-2">Sort</th>
        <th class="px-3 py-2">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($attached as $dt)
      <tr class="border-t">
        <td class="px-3 py-2">{{ $dt->name }}</td>
        <td class="px-3 py-2">
          <form method="POST" action="{{ route('admin.departments.docTypes.update',[$department,$dt]) }}" class="inline-flex items-center gap-2">
            @csrf @method('PATCH')
            <input type="hidden" name="sort_order" value="{{ $dt->pivot->sort_order }}">
            <input type="hidden" name="is_active" value="{{ $dt->pivot->is_active ? 0 : 1 }}">
            <button class="px-2 py-1 rounded {{ $dt->pivot->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
              {{ $dt->pivot->is_active ? 'Active' : 'Inactive' }}
            </button>
          </form>
        </td>
        <td class="px-3 py-2">
          <form method="POST" action="{{ route('admin.departments.docTypes.update',[$department,$dt]) }}" class="inline-flex items-center gap-2">
            @csrf @method('PATCH')
            <input type="number" name="sort_order" value="{{ $dt->pivot->sort_order }}" class="w-24 rounded border px-2 py-1">
            <input type="hidden" name="is_active" value="{{ $dt->pivot->is_active }}">
            <button class="px-2 py-1 rounded bg-slate-800 text-white">Simpan</button>
          </form>
        </td>
        <td class="px-3 py-2 text-right">
          <form method="POST" action="{{ route('admin.departments.docTypes.detach',[$department,$dt]) }}" onsubmit="return confirm('Lepas Doc Type ini?')">
            @csrf @method('DELETE')
            <button class="px-2 py-1 text-red-700 hover:underline">Detach</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="4" class="px-3 py-6 text-center text-slate-500">Belum ada Doc Type terpasang.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
