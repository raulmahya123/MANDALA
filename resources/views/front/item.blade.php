@extends('layouts.app', ['title'=>$department->name.' · '.$docType->name.' · '.$item->name])

@section('content')
<a href="{{ route('browse.list',[$department,$docType]) }}" class="text-sm text-slate-500 hover:text-slate-700">← {{ $docType->name }}</a>
<h1 class="text-xl font-semibold mt-2 mb-4">{{ $item->name }}</h1>

@if($docs->isEmpty())
  <div class="rounded-xl border bg-white p-4">Belum ada dokumen.</div>
@else
  <div class="rounded-xl border overflow-hidden">
    <table class="min-w-full bg-white text-sm">
      <thead class="bg-slate-100 text-left">
        <tr>
          <th class="px-4 py-2">Judul</th>
          <th class="px-4 py-2">Status</th>
          <th class="px-4 py-2">Publish</th>
          <th class="px-4 py-2"></th>
        </tr>
      </thead>
      <tbody>
        @foreach($docs as $doc)
        <tr class="border-t">
          <td class="px-4 py-2">
            <a class="font-medium hover:text-blue-600" href="{{ route('browse.show',$doc) }}">{{ $doc->title }}</a>
            <div class="text-xs text-slate-500">{{ $doc->summary }}</div>
          </td>
          <td class="px-4 py-2">
            <span class="px-2 py-1 rounded text-xs border {{ $doc->status==='open'?'bg-emerald-50 text-emerald-700 border-emerald-200':'bg-slate-50 text-slate-600 border-slate-200' }}">
              {{ strtoupper($doc->status) }}
            </span>
          </td>
          <td class="px-4 py-2 text-xs">{{ optional($doc->published_at)->format('d M Y') }}</td>
          <td class="px-4 py-2 text-right">
            <a href="{{ route('browse.download',$doc) }}" class="px-3 py-1 rounded bg-blue-600 text-white">Download</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $docs->links() }}
  </div>
@endif
@endsection
