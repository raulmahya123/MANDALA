@extends('layouts.app', ['title'=>$department->name.' · '.$docType->name])

@section('content')
<a href="{{ route('browse.department',$department) }}" class="text-sm text-slate-500 hover:text-slate-700">← {{ $department->name }}</a>
<h1 class="text-xl font-semibold mt-2 mb-4">{{ $docType->name }}</h1>

@if($items->isEmpty())
  <div class="rounded-xl border bg-white p-4">Belum ada item.</div>
@else
  <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($items as $it)
      <a href="{{ route('browse.item', [$department,$docType,$it]) }}" class="rounded-xl border bg-white p-4 hover:shadow">
        <div class="font-semibold">{{ $it->name }}</div>
        <div class="text-xs text-slate-500 mt-1">Lihat dokumen</div>
      </a>
    @endforeach
  </div>
@endif
@endsection
