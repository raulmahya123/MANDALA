@extends('layouts.admin')

@section('admin')
<h1 class="text-lg font-semibold mb-4">Tambah Doc Item</h1>
<form method="POST" action="{{ route('admin.doc-items.store') }}" class="space-y-4">
  @csrf
  <div>
    <label class="block text-sm mb-1">Departemen</label>
    <select name="department_id" class="w-full rounded border px-3 py-2" required>
      <option value="">Pilih...</option>
      @foreach($departments as $d)
        <option value="{{ $d->id }}">{{ $d->name }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label class="block text-sm mb-1">Doc Type</label>
    <select name="doc_type_id" class="w-full rounded border px-3 py-2" required>
      <option value="">Pilih...</option>
      @foreach($docTypes as $t)
        <option value="{{ $t->id }}">{{ $t->name }}</option>
      @endforeach
    </select>
  </div>
  <div>
    <label class="block text-sm mb-1">Nama Item</label>
    <input type="text" name="name" class="w-full rounded border px-3 py-2" required>
  </div>
  <div class="flex gap-2">
    <a href="{{ route('admin.doc-items.index') }}" class="px-3 py-2 rounded border">Batal</a>
    <button class="px-3 py-2 rounded bg-blue-600 text-white">Simpan</button>
  </div>
</form>
@endsection
