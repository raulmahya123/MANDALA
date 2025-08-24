@extends('layouts.admin')

@section('page_title','Edit Doc Type')

@push('head')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const nameEl = document.getElementById('name');
    const slugEl = document.getElementById('slug');
    const autoEl = document.getElementById('autoSlug');

    const slugify = str => (str || '')
      .toString()
      .normalize('NFKD')
      .replace(/[\u0300-\u036f]/g, '')
      .toLowerCase()
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/(^-|-$)/g, '');

    const sync = () => { if (autoEl.checked) slugEl.value = slugify(nameEl.value); };

    if (nameEl && slugEl && autoEl) {
      nameEl.addEventListener('input', sync);
      autoEl.addEventListener('change', sync);
    }
  });
</script>
@endpush

@section('admin')
<div class="rounded-2xl border bg-white dark:bg-slate-900 p-4 max-w-xl">
  <h2 class="text-lg font-semibold mb-4">Edit Doc Type</h2>

  @if($errors->any())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 text-red-700 px-4 py-3">
      <ul class="list-disc pl-4">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.doc-types.update',$docType) }}" class="space-y-4">
    @csrf
    @method('PUT')

    <div>
      <label class="block text-sm mb-1">Nama</label>
      <input id="name" type="text" name="name" value="{{ old('name', $docType->name) }}" class="w-full rounded-xl border px-3 py-2" required>
    </div>

    <div>
      <div class="flex items-center justify-between">
        <label class="block text-sm mb-1">Slug (opsional)</label>
        <label class="flex items-center gap-2 text-xs text-slate-500">
          <input id="autoSlug" type="checkbox" class="rounded">
          Auto-slug dari Nama
        </label>
      </div>
      <input id="slug" type="text" name="slug" value="{{ old('slug', $docType->slug) }}" class="w-full rounded-xl border px-3 py-2">
      <p class="text-xs text-slate-500 mt-1">Kosongkan untuk regenerasi dari Nama.</p>
    </div>

    <div class="flex items-center gap-2">
      <a href="{{ route('admin.doc-types.index') }}" class="px-3 py-2 rounded-xl border">Batal</a>
      <button class="px-3 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">Update</button>
    </div>
  </form>
</div>
@endsection
