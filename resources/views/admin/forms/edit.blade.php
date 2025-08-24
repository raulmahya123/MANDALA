{{-- resources/views/admin/forms/edit.blade.php --}}
@extends('layouts.admin')

@section('page_title','Edit Form')

@section('admin')
<div class="rounded-2xl border bg-white dark:bg-slate-900 p-4 max-w-5xl">
  {{-- Header --}}
  <div class="flex items-center justify-between mb-4 gap-2">
    <div>
      <h2 class="text-lg font-semibold">Edit Form</h2>
      <p class="text-xs text-slate-500">Perbarui judul, status, dan kelola field</p>
    </div>
    <a href="{{ route('admin.forms.index') }}"
       class="inline-flex items-center gap-2 px-3 py-2 rounded-xl ring-1 ring-inset ring-gray-300 bg-white text-gray-700 hover:bg-gray-50
              dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-600 dark:hover:bg-slate-700">
      Kembali
    </a>
  </div>

  {{-- Flash & Errors --}}
  @if(session('ok'))
    <div class="mb-3 rounded-xl border border-emerald-200 text-emerald-800 bg-emerald-50 px-4 py-3">
      {{ session('ok') }}
    </div>
  @endif
  @if($errors->any())
    <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 text-amber-900 px-4 py-3">
      <div class="font-semibold mb-1">Periksa kembali input kamu:</div>
      <ul class="list-disc pl-5 text-sm space-y-1">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  {{-- Form: meta --}}
  <form method="POST" action="{{ route('admin.forms.update',$form) }}"
        class="grid md:grid-cols-3 gap-3 mb-6 items-end">
    @csrf @method('PUT')

    <div class="md:col-span-2">
      <label class="block text-sm mb-1">Judul</label>
      <input name="title" value="{{ old('title',$form->title) }}"
             class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950
                    px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500" required>
    </div>

    <div class="flex items-center gap-2">
      <input type="checkbox" name="is_active" value="1" id="is_active"
             class="rounded" {{ old('is_active',$form->is_active) ? 'checked' : '' }}>
      <label for="is_active" class="text-sm">Aktif</label>
    </div>

    <div class="md:col-span-3 flex flex-wrap gap-2 pt-1">
      <button
        class="px-3 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
        Update
      </button>

      <a class="px-3 py-2 rounded-xl ring-1 ring-inset ring-gray-300 bg-white text-gray-700 hover:bg-gray-50
                dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-600 dark:hover:bg-slate-700"
         href="{{ route('admin.forms.export.excel',$form) }}">
        Export Excel
      </a>

      <a class="px-3 py-2 rounded-xl ring-1 ring-inset ring-gray-300 bg-white text-gray-700 hover:bg-gray-50
                dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-600 dark:hover:bg-slate-700"
         href="{{ route('admin.forms.export.pdf',$form) }}">
        Export PDF
      </a>
    </div>
  </form>

  <div class="grid md:grid-cols-2 gap-6">
    {{-- Daftar Field --}}
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/60 dark:bg-slate-900/60 overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800">
        <h3 class="font-semibold">Field</h3>
      </div>

      <div class="divide-y divide-slate-200 dark:divide-slate-800 text-sm">
        @forelse($form->fields as $f)
          <div class="px-4 py-3 flex items-start justify-between gap-3">
            <div class="min-w-0">
              <div class="font-medium">
                #{{ $f->sort_order }} — {{ $f->label }}
                <span class="text-xs text-slate-500">({{ $f->name }})</span>
              </div>
              <div class="text-xs text-slate-500">
                Tipe: <span class="font-mono">{{ $f->type }}</span>
                @if($f->required) • <span class="text-rose-600">required</span>@endif
              </div>
            </div>
            <form method="POST" action="{{ route('admin.forms.fields.destroy',[$form,$f]) }}"
                  onsubmit="return confirm('Hapus field?')">
              @csrf @method('DELETE')
              <button
                class="px-3 py-1.5 rounded-2xl ring-1 ring-inset ring-rose-300 bg-white text-rose-600 hover:bg-rose-50
                       dark:bg-slate-800 dark:text-rose-400 dark:ring-rose-700 dark:hover:bg-slate-700">
                Hapus
              </button>
            </form>
          </div>
        @empty
          <div class="px-4 py-6 text-center text-slate-500">Tidak ada field.</div>
        @endforelse
      </div>
    </div>

    {{-- Tambah Field --}}
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white/60 dark:bg-slate-900/60 overflow-hidden">
      <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-800">
        <h3 class="font-semibold">Tambah Field</h3>
      </div>

      <form method="POST" action="{{ route('admin.forms.fields.store',$form) }}" class="p-4 grid gap-3 text-sm">
        @csrf

        <div>
          <label class="block text-xs mb-1">Label</label>
          <input name="label" value="{{ old('label') }}"
                 class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950
                        px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500" required>
        </div>

        <div>
          <label class="block text-xs mb-1">Nama (unik, otomatis di-slug)</label>
          <input name="name" value="{{ old('name') }}"
                 class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950
                        px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500" required>
        </div>

        <div>
          <label class="block text-xs mb-1">Tipe</label>
          <select name="type"
                  class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950
                         px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option>text</option>
            <option>textarea</option>
            <option>number</option>
            <option>date</option>
            <option>select</option>
            <option>checkbox</option>
          </select>
        </div>

        <div>
          <label class="block text-xs mb-1">Options (JSON) — untuk select/checkbox</label>
          <textarea name="options" rows="3" placeholder='Contoh: ["A","B"]'
                    class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950
                           px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ old('options') }}</textarea>
        </div>

        <div class="flex items-center gap-2">
          <input type="checkbox" id="required" name="required" value="1" class="rounded" {{ old('required')?'checked':'' }}>
          <label for="required">Required</label>
        </div>

        <div>
          <label class="block text-xs mb-1">Urutan</label>
          <input type="number" name="sort_order" value="{{ old('sort_order',0) }}"
                 class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950
                        px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        <div class="pt-1">
          <button
            class="px-3 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            Tambah
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
