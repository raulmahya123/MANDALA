@extends('layouts.app', ['title'=>$document->title])

@section('content')
<a href="{{ url()->previous() }}" class="text-sm text-slate-500 hover:text-slate-700">← Kembali</a>
<h1 class="text-xl font-semibold mt-2">{{ $document->title }}</h1>
<div class="text-sm text-slate-500 mb-4">
  {{ $document->department->name }} · {{ $document->docType->name }} · {{ optional($document->item)->name }}
</div>
<div class="rounded-xl border bg-white p-4">
  <p class="mb-4">{{ $document->summary }}</p>
  <a href="{{ route('browse.download',$document) }}" class="px-4 py-2 rounded bg-blue-600 text-white">Download Dokumen</a>
</div>
@endsection
