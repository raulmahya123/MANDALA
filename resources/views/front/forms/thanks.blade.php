@extends('layouts.app')


@section('content')
<div class="max-w-2xl mx-auto">
  <div class="rounded-2xl border bg-white dark:bg-slate-900 p-6 text-center">
    <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-2xl bg-emerald-100 text-emerald-700 mb-3">
      ✓
    </div>
    <h2 class="text-xl font-semibold mb-2">Terima kasih!</h2>
    <p class="text-slate-600 dark:text-slate-300">Form <b>{{ $form->title }}</b> sudah dikirim untuk ditinjau.</p>
    <div class="mt-4">
      <a href="{{ route('home') }}" class="px-3 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">Kembali ke Beranda</a>
    </div>
  </div>
</div>
@endsection
