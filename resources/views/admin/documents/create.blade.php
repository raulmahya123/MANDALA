@extends('layouts.admin')

@section('admin')
<h1 class="text-lg font-semibold mb-4">Tambah Document</h1>
<form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data" class="space-y-4">
  @csrf
  <div class="grid md:grid-cols-3 gap-4">
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
      <label class="block text-sm mb-1">Item (opsional)</label>
      <select name="doc_item_id" class="w-full rounded border px-3 py-2">
        <option value="">-</option>
        @foreach($items as $it)
          <option value="{{ $it->id }}">{{ $it->department->name }} · {{ $it->docType->name }} · {{ $it->name }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div>
    <label class="block text-sm mb-1">Judul</label>
    <input type="text" name="title" class="w-full rounded border px-3 py-2" required>
  </div>
  <div>
    <label class="block text-sm mb-1">Ringkasan</label>
    <textarea name="summary" rows="3" class="w-full rounded border px-3 py-2"></textarea>
  </div>
  <div class="grid md:grid-cols-3 gap-4">
    <div>
      <label class="block text-sm mb-1">File</label>
      <input type="file" name="file" class="w-full rounded border px-3 py-2" required>
    </div>
    <div>
      <label class="block text-sm mb-1">Status</label>
      <select name="status" class="w-full rounded border px-3 py-2">
        <option value="draft">Draft</option>
        <option value="open">Open</option>
        <option value="archived">Archived</option>
      </select>
    </div>
    <div>
      <label class="block text-sm mb-1">Published At</label>
      <input type="datetime-local" name="published_at" class="w-full rounded border px-3 py-2">
    </div>
  </div>

  <div class="flex gap-2">
    <a href="{{ route('admin.documents.index') }}" class="px-3 py-2 rounded border">Batal</a>
    <button class="px-3 py-2 rounded bg-blue-600 text-white">Simpan</button>
  </div>
</form>
@endsection
