@extends('layouts.admin')

@section('page_title','Tambah Document')

@section('admin')
<div class="rounded-2xl border bg-white dark:bg-slate-900 p-4 max-w-4xl">
  <div class="flex items-center justify-between mb-4 gap-2">
    <div>
      <h2 class="text-lg font-semibold">Tambah Document</h2>
      <p class="text-xs text-slate-500">Unggah dokumen dan tentukan metadata-nya</p>
    </div>
    <a href="{{ route('admin.documents.index') }}"
       class="inline-flex items-center gap-2 px-3 py-2 rounded-xl ring-1 ring-inset ring-gray-300 bg-white text-gray-700 hover:bg-gray-50
              dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-600 dark:hover:bg-slate-700">
      Kembali
    </a>
  </div>

  @if($errors->any())
    <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 text-amber-900 px-4 py-3">
      <div class="font-semibold mb-1">Periksa kembali input kamu:</div>
      <ul class="list-disc pl-5 text-sm space-y-1">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.documents.store') }}" enctype="multipart/form-data" class="grid gap-4">
    @csrf

    <div class="grid md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm mb-1">Departemen</label>
        <select name="department_id"
                class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                required>
          <option value="">Pilih...</option>
          @foreach($departments as $d)
            <option value="{{ $d->id }}" @selected(old('department_id')==$d->id)>{{ $d->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-sm mb-1">Doc Type</label>
        <select name="doc_type_id"
                class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                required>
          <option value="">Pilih...</option>
          @foreach($docTypes as $t)
            <option value="{{ $t->id }}" @selected(old('doc_type_id')==$t->id)>{{ $t->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="block text-sm mb-1">Item (opsional)</label>
        <select name="doc_item_id"
                class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
          <option value="">-</option>
          @foreach($items as $it)
            <option value="{{ $it->id }}" @selected(old('doc_item_id')==$it->id)>
              {{ $it->department->name }} · {{ $it->docType->name }} · {{ $it->name }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    <div>
      <label class="block text-sm mb-1">Judul</label>
      <input id="title" type="text" name="title" value="{{ old('title') }}"
             class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
             required>
    </div>

    {{-- Slug (opsional) --}}
    <div>
      <div class="flex items-center justify-between">
        <label class="block text-sm mb-1">Slug (opsional)</label>
        <label class="flex items-center gap-2 text-xs text-slate-500">
          <input id="autoSlug" type="checkbox" class="rounded" checked>
          Auto-slug dari Judul
        </label>
      </div>
      <input id="slug" type="text" name="slug" value="{{ old('slug') }}"
             class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
             placeholder="contoh: panduan-pelatihan">
      <p class="text-xs text-slate-500 mt-1">Kosongkan bila ingin otomatis.</p>
    </div>

    <div>
      <label class="block text-sm mb-1">Ringkasan</label>
      <textarea name="summary" rows="3"
                class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                placeholder="Opsional">{{ old('summary') }}</textarea>
    </div>

    <div class="grid md:grid-cols-3 gap-4">
      <div>
        <label class="block text-sm mb-1">File</label>
        <input type="file" name="file"
               class="block w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950 px-3 py-2 focus:outline-none"
               required>
        <p class="text-xs text-slate-500 mt-1">Format umum: PDF/DOCX/IMG. Maks 10–20MB (sesuaikan validasi).</p>
      </div>

      <div>
        <label class="block text-sm mb-1">Status</label>
        <select name="status"
                class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
          <option value="draft" @selected(old('status')==='draft')>Draft</option>
          <option value="open" @selected(old('status')==='open')>Open</option>
          <option value="archived" @selected(old('status')==='archived')>Archived</option>
        </select>
      </div>

      <div>
        <label class="block text-sm mb-1">Published At</label>
        <input type="datetime-local" name="published_at" value="{{ old('published_at') }}"
               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
        <p class="text-xs text-slate-500 mt-1">Kosongkan bila belum dipublish.</p>
      </div>
    </div>

    {{-- is_active --}}
    <div class="flex items-center gap-2">
      <input type="checkbox" id="is_active" name="is_active" value="1"
             class="rounded" {{ old('is_active', true) ? 'checked' : '' }}>
      <label for="is_active" class="text-sm">Aktif</label>
    </div>

    <div class="flex items-center gap-2 pt-2">
      <a href="{{ route('admin.documents.index') }}"
         class="px-3 py-2 rounded-xl ring-1 ring-inset ring-gray-300 bg-white text-gray-700 hover:bg-gray-50
                dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-600 dark:hover:bg-slate-700">
        Batal
      </a>
      <button
        class="px-3 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
        Simpan
      </button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const titleEl = document.getElementById('title');
  const slugEl  = document.getElementById('slug');
  const autoEl  = document.getElementById('autoSlug');

  const slugify = s => (s||'')
    .normalize('NFKD').replace(/[\u0300-\u036f]/g,'')
    .toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'');

  const sync = () => { if (autoEl?.checked) slugEl.value = slugify(titleEl.value); };

  if (titleEl && slugEl && autoEl) {
    titleEl.addEventListener('input', sync);
    autoEl.addEventListener('change', sync);
    sync(); // initial
  }
});
</script>
@endpush
