@extends('layouts.admin')

@section('admin')
<h1 class="text-lg font-semibold mb-4">Tambah Department</h1>
<form method="POST" action="{{ route('admin.departments.store') }}" class="space-y-4">
  @csrf
  <div>
    <label class="block text-sm mb-1">Nama</label>
    <input type="text" name="name" class="w-full rounded border px-3 py-2" required>
  </div>
  <div class="flex gap-2">
    <a href="{{ route('admin.departments.index') }}" class="px-3 py-2 rounded border">Batal</a>
    <button class="px-3 py-2 rounded bg-blue-600 text-white">Simpan</button>
  </div>
</form>
@endsection
