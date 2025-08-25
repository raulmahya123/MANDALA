@extends('layouts.admin')
@section('title','Edit Department')

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

<form method="POST" action="{{ route('admin.departments.update',$department) }}" class="max-w-xl rounded-xl border bg-white p-4 space-y-4">
  @csrf @method('PUT')
  <div>
    <label class="block mb-1">Nama</label>
    <input type="text" name="name" value="{{ old('name',$department->name) }}" class="w-full rounded border px-3 py-2" required>
  </div>
  <div>
    <label class="block mb-1">Slug</label>
    <input type="text" name="slug" value="{{ old('slug',$department->slug) }}" class="w-full rounded border px-3 py-2">
  </div>
  <div class="flex items-center gap-2">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active',$department->is_active)?'checked':'' }}>
    <label for="is_active">Active</label>
  </div>
  <div class="flex gap-2">
    <button class="px-3 py-2 rounded bg-emerald-600 text-white">Simpan</button>
    <a href="{{ route('admin.departments.index') }}" class="px-3 py-2 rounded border">Kembali</a>
  </div>
</form>

<div class="mt-6 flex flex-wrap gap-3">
  <a href="{{ route('admin.departments.members',$department) }}" class="px-3 py-2 rounded border">Kelola Members</a>
  <a href="{{ route('admin.departments.docTypes',$department) }}" class="px-3 py-2 rounded border">Kelola Doc Types</a>
  <a href="{{ route('admin.departments.access',$department) }}" class="px-3 py-2 rounded border">Kelola Access</a>
</div>
@endsection
