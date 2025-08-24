@extends('layouts.app', ['title'=>$department->name])

@section('content')
<a href="{{ route('home') }}" class="text-sm text-slate-500 hover:text-slate-700">← Kembali</a>
<h1 class="text-xl font-semibold mt-2 mb-4">{{ $department->name }}</h1>

<div class="grid md:grid-cols-3 gap-4">
  @foreach($docTypes as $t)
    <a href="{{ route('browse.list', [$department,$t]) }}" class="rounded-xl border bg-white p-4 hover:shadow">
      <div class="font-semibold">{{ $t->name }}</div>
      <div class="text-xs text-slate-500 mt-1">Lihat item</div>
    </a>
  @endforeach
</div>
@endsection
