@extends('layouts.admin')

@section('admin')
<h1 class="text-lg font-semibold mb-4">Edit Doc Item</h1>

<form method="POST" action="{{ route('admin.doc-items.update',$docItem) }}" class="space-y-4 text-sm">
  @csrf
  @method('PUT')

  <div class="grid md:grid-cols-2 gap-4">
    <div>
      <label class="block mb-1">Departemen</label>
      <input type="text" value="{{ $docItem->department->name }}" class="w-full rounded border px-3 py-2 bg-slate-100" disabled>
      <p class="text-xs text-slate-500 mt-1">Departemen tidak diubah di halaman ini.</p>
    </div>

    <div>
      <label class="block mb-1">Doc Type</label>
      <input type="text" value="{{ $docItem->docType->name }}" class="w-full rounded border px-3 py-2 bg-slate-100" disabled>
      <p class="text-xs text-slate-500 mt-1">Doc Type tidak diubah di halaman ini.</p>
    </div>
  </div>

  <div>
    <label class="block mb-1">Nama Item</label>
    <input type="text" name="name" value="{{ old('name',$docItem->name) }}" class="w-full rounded border px-3 py-2" required>
  </div>

  <div class="flex gap-2">
    <a href="{{ route('admin.doc-items.index') }}" class="px-3 py-2 rounded border">Kembali</a>
    <button class="px-3 py-2 rounded bg-blue-600 text-white">Simpan</button>
  </div>
</form>
@endsection
