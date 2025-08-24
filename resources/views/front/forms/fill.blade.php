@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
  <div class="rounded-2xl border bg-white dark:bg-slate-900 p-4">
    <div class="flex items-center justify-between gap-2 mb-4">
      <div>
        <h1 class="text-lg font-semibold">{{ $form->title }}</h1>
        <p class="text-xs text-slate-500">
          {{ $form->department->name ?? '—' }} · {{ $form->docType->name ?? '—' }} · {{ $form->item->name ?? '—' }}
        </p>
      </div>
      <a href="{{ route('home') }}"
         class="inline-flex items-center gap-2 px-3 py-2 rounded-xl ring-1 ring-inset ring-gray-300 bg-white text-gray-700 hover:bg-gray-50
                dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-600 dark:hover:bg-slate-700">
        Kembali
      </a>
    </div>

    {{-- alert error --}}
    @if ($errors->any())
      <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 text-amber-900 px-4 py-3">
        <div class="font-semibold mb-1">Periksa kembali input kamu:</div>
        <ul class="list-disc list-inside text-sm space-y-1">
          @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('form.submit', $form) }}" class="grid gap-4">
      @csrf

      @forelse($form->fields as $field)
        @php
          $name = $field->name;
          $label = $field->label ?? $name;
          $required = (bool)($field->required ?? false);
          $type = $field->type ?? 'text';
          $opts = [];
          if ($type === 'select' || $type === 'checkbox') {
            try { $opts = json_decode($field->options ?? '[]', true) ?: []; } catch (\Throwable $e) { $opts = []; }
          }
        @endphp

        {{-- TEXT --}}
        @if($type==='text')
          <div>
            <label class="block text-sm mb-1">
              {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
            </label>
            <input type="text" name="{{ $name }}" value="{{ old($name) }}"
                   class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950
                          px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                   @if($required) required @endif>
            @error($name)<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
          </div>
        @endif

        {{-- TEXTAREA --}}
        @if($type==='textarea')
          <div>
            <label class="block text-sm mb-1">
              {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
            </label>
            <textarea name="{{ $name }}" rows="4"
                      class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950
                             px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                      @if($required) required @endif>{{ old($name) }}</textarea>
            @error($name)<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
          </div>
        @endif

        {{-- NUMBER --}}
        @if($type==='number')
          <div>
            <label class="block text-sm mb-1">
              {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
            </label>
            <input type="number" name="{{ $name }}" value="{{ old($name) }}"
                   class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950
                          px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                   @if($required) required @endif>
            @error($name)<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
          </div>
        @endif

        {{-- DATE --}}
        @if($type==='date')
          <div>
            <label class="block text-sm mb-1">
              {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
            </label>
            <input type="date" name="{{ $name }}" value="{{ old($name) }}"
                   class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950
                          px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                   @if($required) required @endif>
            @error($name)<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
          </div>
        @endif

        {{-- SELECT --}}
        @if($type==='select')
          <div>
            <label class="block text-sm mb-1">
              {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
            </label>
            <select name="{{ $name }}"
                    class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950
                           px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    @if($required) required @endif>
              <option value="">— pilih —</option>
              @foreach($opts as $opt)
                <option value="{{ $opt }}" @selected(old($name)==$opt)>{{ $opt }}</option>
              @endforeach
            </select>
            @error($name)<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
          </div>
        @endif

        {{-- CHECKBOX (multi) --}}
        @if($type==='checkbox')
          <div>
            <div class="block text-sm mb-1">
              {{ $label }} @if($required)<span class="text-rose-500">*</span>@endif
            </div>
            <div class="grid sm:grid-cols-2 gap-2">
              @foreach($opts as $opt)
                <label class="inline-flex items-center gap-2 text-sm rounded-xl border px-3 py-2 bg-white dark:bg-slate-950
                               border-slate-200 dark:border-slate-700">
                  <input type="checkbox" name="{{ $name }}[]" value="{{ $opt }}"
                         class="rounded"
                         @checked( in_array($opt, (array)old($name, [])) )>
                  <span>{{ $opt }}</span>
                </label>
              @endforeach
            </div>
            @error($name)<p class="text-xs text-rose-500 mt-1">{{ $message }}</p>@enderror
          </div>
        @endif

      @empty
        <div class="text-sm text-slate-500">Form ini belum memiliki field.</div>
      @endforelse

      <div class="flex items-center gap-2 pt-2">
        <button name="draft" value="1"
                class="px-3 py-2 rounded-xl ring-1 ring-inset ring-gray-300 bg-white text-gray-700 hover:bg-gray-50
                       dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-600 dark:hover:bg-slate-700">
          Simpan Draft
        </button>
        <button name="submit" value="1"
                class="px-3 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">
          Kirim
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
