@extends('layouts.admin')

@section('page_title','Tambah Doc Type')

@section('admin')
<div class="rounded-2xl border bg-white dark:bg-slate-900 p-4 max-w-xl">
  <h2 class="text-lg font-semibold mb-4">Tambah Doc Type</h2>

  @if($errors->any())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 text-red-700 px-4 py-3">
      <ul class="list-disc pl-4">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.doc-types.store') }}" class="space-y-4">
    @csrf
    <div>
      <label class="block text-sm mb-1">Nama</label>
      <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-xl border px-3 py-2" required>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.doc-types.index') }}" class="px-3 py-2 rounded-xl border">Batal</a>
      <button class="px-3 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">Simpan</button>
    </div>
  </form>
</div>
@endsection
