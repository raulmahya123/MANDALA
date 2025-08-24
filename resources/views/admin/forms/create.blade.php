{{-- resources/views/admin/forms/create.blade.php --}}
@extends('layouts.admin')

@section('page_title','Buat Form')

@section('admin')
<div class="rounded-2xl border bg-white dark:bg-slate-900 p-4 max-w-3xl">
  <div class="flex items-center justify-between mb-4 gap-2">
    <div>
      <h2 class="text-lg font-semibold">Buat Form</h2>
      <p class="text-xs text-slate-500">Tetapkan scope (Departemen/Type/Item) dan judul form</p>
    </div>
    <a href="{{ route('admin.forms.index') }}"
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

  <form method="POST" action="{{ route('admin.forms.store') }}" class="grid md:grid-cols-2 gap-4">
    @csrf

    <div>
      <label class="block text-sm mb-1">Departemen</label>
      <select name="department_id"
              class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950
                     px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
              required>
        <option value="">Pilih…</option>
        @foreach($departments as $d)
          <option value="{{ $d->id }}" @selected(old('department_id')==$d->id)>{{ $d->name }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-sm mb-1">Doc Type</label>
      <select name="doc_type_id"
              class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950
                     px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
              required>
        <option value="">Pilih…</option>
        @foreach($docTypes as $t)
          <option value="{{ $t->id }}" @selected(old('doc_type_id')==$t->id)>{{ $t->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="md:col-span-2">
      <label class="block text-sm mb-1">Item (opsional)</label>
      <select name="doc_item_id"
              class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950
                     px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
        <option value="">-</option>
        @foreach($items as $it)
          <option value="{{ $it->id }}" @selected(old('doc_item_id')==$it->id)>
            {{ $it->department->name }} · {{ $it->docType->name }} · {{ $it->name }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="md:col-span-2">
      <label class="block text-sm mb-1">Judul</label>
      <input name="title" value="{{ old('title') }}"
             class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950
                    px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
             required>
    </div>

    <div class="md:col-span-2">
      <label class="inline-flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1"
               class="rounded" {{ old('is_active', true) ? 'checked' : '' }}>
        <span>Aktif</span>
      </label>
    </div>

    <div class="md:col-span-2 flex items-center gap-2 pt-2">
      <a href="{{ route('admin.forms.index') }}"
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
