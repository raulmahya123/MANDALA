@extends('layouts.admin')
@section('title','Tambah Department')

@section('admin')
@if($errors->any())
  <div class="mb-3 p-3 rounded bg-red-50 border border-red-200 text-red-800">
    <ul class="list-disc ml-5">
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('admin.departments.store') }}" class="max-w-xl rounded-xl border bg-white p-4 space-y-4">
  @csrf
  <div>
    <label class="block mb-1">Nama</label>
    <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded border px-3 py-2" required>
  </div>
  <div>
    <label class="block mb-1">Slug (opsional)</label>
    <input type="text" name="slug" value="{{ old('slug') }}" class="w-full rounded border px-3 py-2" placeholder="auto jika kosong">
  </div>
  <div class="flex items-center gap-2">
    <input type="checkbox" name="is_active" value="1" id="is_active" checked>
    <label for="is_active">Active</label>
  </div>
  <div class="flex gap-2">
    <button class="px-3 py-2 rounded bg-emerald-600 text-white">Simpan</button>
    <a href="{{ route('admin.departments.index') }}" class="px-3 py-2 rounded border">Batal</a>
  </div>
</form>
@endsection
