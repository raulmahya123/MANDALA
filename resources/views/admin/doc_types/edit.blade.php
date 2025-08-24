@extends('layouts.admin')

@section('admin')
<h1 class="text-lg font-semibold mb-4">Edit Doc Item</h1>
<form method="POST" action="{{ route('admin.doc-items.update',$docItem) }}" class="space-y-4">
  @csrf @method('PUT')
  <div>
    <label class="block text-sm mb-1">Nama Item</label>
    <input type="text" name="name" value="{{ old('name',$docItem->name) }}" class="w-full rounded border px-3 py-2" required>
  </div>
  <div class="flex gap-2">
    <a href="{{ route('admin.doc-items.index') }}" class="px-3 py-2 rounded border">Batal</a>
    <button class="px-3 py-2 rounded bg-blue-600 text-white">Simpan</button>
  </div>
</form>
@endsection
