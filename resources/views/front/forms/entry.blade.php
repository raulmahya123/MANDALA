@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
  <div class="rounded-2xl border bg-white dark:bg-slate-900 p-4">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h1 class="text-lg font-semibold">{{ $form->title }}</h1>
        <p class="text-xs text-slate-500">
          Entry #{{ $entry->id }} · Status: <span class="font-medium">{{ ucfirst($entry->status) }}</span>
        </p>
      </div>
      <a href="{{ route('home') }}"
         class="inline-flex items-center gap-2 px-3 py-2 rounded-xl ring-1 ring-inset ring-gray-300 bg-white text-gray-700 hover:bg-gray-50
                dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-600 dark:hover:bg-slate-700">
        Kembali
      </a>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="border-b text-slate-500">
            <th class="py-2 px-3 text-left">Field</th>
            <th class="py-2 px-3 text-left">Nilai</th>
          </tr>
        </thead>
        <tbody>
          @foreach($form->fields as $f)
            @php
              $val = optional($entry->values->firstWhere('form_field_id', $f->id))->value;
              // jika checkbox disimpan json string → decode
              if ($f->type==='checkbox' && is_string($val)) {
                try { $decoded = json_decode($val, true) ?: []; $val = implode(', ', $decoded); } catch (\Throwable $e) {}
              }
            @endphp
            <tr class="border-b">
              <td class="py-2 px-3 font-medium">{{ $f->label ?? $f->name }}</td>
              <td class="py-2 px-3 text-slate-700 dark:text-slate-200">{{ $val ?? '—' }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @if($entry->approvals?->count())
      <div class="mt-4">
        <h3 class="text-sm font-semibold mb-2">Jejak Persetujuan</h3>
        <div class="rounded-xl border bg-white dark:bg-slate-950 p-3 text-sm">
          @foreach($entry->approvals as $ap)
            <div class="flex items-center justify-between border-b last:border-none py-2">
              <div>
                <div class="font-medium">{{ ucfirst($ap->action) }}</div>
                <div class="text-xs text-slate-500">{{ $ap->notes ?? '—' }}</div>
              </div>
              <div class="text-xs text-slate-500">
                {{ optional($ap->created_at)->format('d M Y H:i') }}
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</div>
@endsection
