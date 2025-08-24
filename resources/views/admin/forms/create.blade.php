@extends('layouts.admin')
@section('admin')
<h1 class="text-lg font-semibold mb-4">Buat Form</h1>
<form method="POST" action="{{ route('admin.forms.store') }}" class="grid md:grid-cols-2 gap-4">
  @csrf
  <div>
    <label class="block text-sm mb-1">Departemen</label>
    <select name="department_id" class="w-full rounded border px-3 py-2" required>
      @foreach($departments as $d) <option value="{{ $d->id }}">{{ $d->name }}</option> @endforeach
    </select>
  </div>
  <div>
    <label class="block text-sm mb-1">Doc Type</label>
    <select name="doc_type_id" class="w-full rounded border px-3 py-2" required>
      @foreach($docTypes as $t) <option value="{{ $t->id }}">{{ $t->name }}</option> @endforeach
    </select>
  </div>
  <div class="md:col-span-2">
    <label class="block text-sm mb-1">Item (opsional)</label>
    <select name="doc_item_id" class="w-full rounded border px-3 py-2">
      <option value="">-</option>
      @foreach($items as $it) <option value="{{ $it->id }}">{{ $it->department->name }} · {{ $it->docType->name }} · {{ $it->name }}</option> @endforeach
    </select>
  </div>
  <div class="md:col-span-2">
    <label class="block text-sm mb-1">Judul</label>
    <input name="title" class="w-full rounded border px-3 py-2" required>
  </div>
  <label class="inline-flex items-center gap-2">
    <input type="checkbox" name="is_active" value="1"> <span>Aktif</span>
  </label>
  <div class="md:col-span-2">
    <button class="px-3 py-2 rounded bg-blue-600 text-white">Simpan</button>
  </div>
</form>
@endsection
