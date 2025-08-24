@extends('layouts.app', ['title'=>'Mandala'])

@section('content')
<h1 class="text-xl font-semibold mb-4">Departemen</h1>
<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
  @foreach($departments as $d)
    <a href="{{ route('browse.department',$d) }}" class="group rounded-xl border bg-white p-4 hover:shadow">
      <div class="font-semibold group-hover:text-blue-600">{{ $d->name }}</div>
      <div class="mt-2 text-xs text-slate-500">
        {{ $d->docTypes->count() }} kategori aktif
      </div>
    </a>
  @endforeach
</div>
@endsection
