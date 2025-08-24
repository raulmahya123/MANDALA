@extends('layouts.app')


@section('content')
<div class="max-w-6xl mx-auto">
  <div class="mb-6">
    <h1 class="text-2xl font-extrabold tracking-tight">Form Pertambangan</h1>
    <p class="text-sm text-slate-500">Pilih form yang ingin kamu isi.</p>
  </div>

  @if(($forms ?? collect())->count())
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
      @foreach($forms as $f)
        <a href="{{ route('form.fill',$f) }}"
           class="group rounded-2xl border border-slate-200 hover:border-emerald-300 bg-white p-4 transition hover:shadow-lg">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="text-xs text-slate-500">
                {{ $f->department->name ?? '—' }} · {{ $f->docType->name ?? '—' }}
              </div>
              <h3 class="mt-1 font-semibold group-hover:text-emerald-700">{{ $f->title }}</h3>
              <div class="text-[11px] text-slate-500 mt-1">
                Item: {{ $f->item->name ?? '—' }}
              </div>
            </div>
            <span class="inline-flex items-center rounded-full bg-emerald-50 text-emerald-700 text-[11px] px-2 py-1 border border-emerald-200">
              Aktif
            </span>
          </div>
          <div class="mt-4 text-xs text-slate-500">Klik untuk mulai mengisi →</div>
        </a>
      @endforeach
    </div>

    @if(method_exists($forms,'links'))
      <div class="mt-6">{{ $forms->links() }}</div>
    @endif
  @else
    <div class="rounded-2xl border bg-white p-6 text-center text-slate-500">
      Belum ada form yang aktif.
    </div>
  @endif
</div>
@endsection
