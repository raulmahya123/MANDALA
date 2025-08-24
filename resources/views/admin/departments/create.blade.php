@extends('layouts.admin')

@section('admin')
<h1 class="text-lg font-semibold mb-4">Tambah Department</h1>

<form method="POST" action="{{ route('admin.departments.store') }}" class="space-y-4">
  @csrf

  {{-- Nama --}}
  <div>
    <label class="block text-sm mb-1">Nama</label>
    <input id="name" type="text" name="name" value="{{ old('name') }}"
           class="w-full rounded border px-3 py-2" required>
  </div>

  {{-- Slug --}}
  <div>
    <div class="flex items-center justify-between">
      <label class="block text-sm mb-1">Slug (opsional)</label>
      <label class="flex items-center gap-2 text-xs text-slate-500">
        <input id="autoSlug" type="checkbox" class="rounded" checked>
        Auto-slug dari Nama
      </label>
    </div>
    <input id="slug" type="text" name="slug" value="{{ old('slug') }}"
           placeholder="contoh: hrga"
           class="w-full rounded border px-3 py-2">
    <p class="text-xs text-slate-500 mt-1">Huruf kecil, angka, dan tanda minus. Kosongkan jika ingin otomatis.</p>
  </div>

  {{-- Status Aktif --}}
  <div class="flex items-center gap-2">
    <input type="checkbox" id="is_active" name="is_active" value="1"
           class="rounded" {{ old('is_active', true) ? 'checked' : '' }}>
    <label for="is_active" class="text-sm">Aktif</label>
  </div>

  {{-- Tombol --}}
  <div class="flex gap-2">
    <a href="{{ route('admin.departments.index') }}" class="px-3 py-2 rounded border">Batal</a>
    <button class="px-3 py-2 rounded bg-blue-600 text-white">Simpan</button>
  </div>
</form>

{{-- Auto generate slug --}}
@push('scripts')
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

  const sync = () => { if (autoEl && autoEl.checked) slugEl.value = slugify(nameEl.value) };

  if (nameEl && slugEl && autoEl) {
    nameEl.addEventListener('input', sync);
    autoEl.addEventListener('change', sync);
    sync();
  }
});
</script>
@endpush

@endsection
