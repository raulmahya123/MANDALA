@extends('layouts.admin')

@section('page_title','Edit Doc Item')

@section('admin')
<div class="rounded-2xl border bg-white dark:bg-slate-900 p-4 max-w-2xl">
  <h2 class="text-lg font-semibold mb-4">Edit Doc Item</h2>

  @if($errors->any())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 text-red-700 px-4 py-3">
      <ul class="list-disc pl-4">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.doc-items.update',$docItem) }}" class="grid gap-4 md:grid-cols-2">
    @csrf @method('PUT')

    <div>
      <label class="block text-sm mb-1">Departemen</label>
      <select name="department_id" class="w-full rounded-xl border px-3 py-2" required>
        @foreach($departments as $d)
          <option value="{{ $d->id }}" @selected(old('department_id',$docItem->department_id)==$d->id)>{{ $d->name }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-sm mb-1">Doc Type</label>
      <select name="doc_type_id" class="w-full rounded-xl border px-3 py-2" required>
        @foreach($docTypes as $t)
          <option value="{{ $t->id }}" @selected(old('doc_type_id',$docItem->doc_type_id)==$t->id)>{{ $t->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="md:col-span-2">
      <label class="block text-sm mb-1">Nama Item</label>
      <input id="name" type="text" name="name"
             value="{{ old('name',$docItem->name) }}"
             class="w-full rounded-xl border px-3 py-2" required>
    </div>

    <div class="md:col-span-2">
      <div class="flex items-center justify-between">
        <label class="block text-sm mb-1">Slug (opsional)</label>
        <label class="flex items-center gap-2 text-xs text-slate-500">
          <input id="autoSlug" type="checkbox" class="rounded">
          Auto-slug dari Nama
        </label>
      </div>
      <input id="slug" type="text" name="slug"
             value="{{ old('slug',$docItem->slug) }}"
             class="w-full rounded-xl border px-3 py-2"
             placeholder="contoh: instruksi-kerja">
      <p class="text-xs text-slate-500 mt-1">Huruf kecil, angka, minus. Kosongkan bila ingin otomatis.</p>
    </div>

    <div class="md:col-span-2 flex items-center gap-2">
      <input type="checkbox" id="is_active" name="is_active" value="1"
             class="rounded" {{ old('is_active',$docItem->is_active) ? 'checked' : '' }}>
      <label for="is_active" class="text-sm">Aktif</label>
    </div>

    <div class="md:col-span-2 flex items-center gap-2">
      <a href="{{ route('admin.doc-items.index') }}" class="px-3 py-2 rounded-xl border">Batal</a>
      <button class="px-3 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">Update</button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const nameEl = document.getElementById('name');
  const slugEl = document.getElementById('slug');
  const autoEl = document.getElementById('autoSlug');

  const slugify = str => (str||'')
    .normalize('NFKD').replace(/[\u0300-\u036f]/g,'')
    .toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'');

  const sync = () => { if (autoEl && autoEl.checked) slugEl.value = slugify(nameEl.value); };

  if (nameEl && slugEl && autoEl) {
    nameEl.addEventListener('input', sync);
    autoEl.addEventListener('change', sync);
  }
});
</script>
@endpush
