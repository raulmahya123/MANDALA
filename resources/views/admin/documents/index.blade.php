@extends('layouts.admin')

@section('admin')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-lg font-semibold">Documents</h1>
  <a href="{{ route('admin.documents.create') }}" class="px-3 py-2 rounded bg-blue-600 text-white">Tambah</a>
</div>
<div class="rounded-xl border overflow-hidden bg-white">
  <table class="min-w-full text-sm">
    <thead class="bg-slate-100 text-left">
      <tr>
        <th class="px-4 py-2">Judul</th>
        <th class="px-4 py-2">Dept/Type/Item</th>
        <th class="px-4 py-2">Status</th>
        <th class="px-4 py-2">Publish</th>
        <th class="px-4 py-2"></th>
      </tr>
    </thead>
    <tbody>
      @foreach($docs as $doc)
      <tr class="border-t">
        <td class="px-4 py-2 font-medium">{{ $doc->title }}</td>
        <td class="px-4 py-2">
          <div class="text-xs text-slate-600">
            {{ $doc->department->name }} · {{ $doc->docType->name }} · {{ optional($doc->item)->name ?? '-' }}
          </div>
        </td>
        <td class="px-4 py-2">
          <span class="px-2 py-1 rounded text-xs border {{ $doc->status==='open'?'bg-emerald-50 text-emerald-700 border-emerald-200':'bg-slate-50 text-slate-600 border-slate-200' }}">
            {{ strtoupper($doc->status) }}
          </span>
        </td>
        <td class="px-4 py-2 text-xs">{{ optional($doc->published_at)->format('d M Y') }}</td>
        <td class="px-4 py-2 text-right">
          <a href="{{ route('admin.documents.edit',$doc) }}" class="px-2 text-blue-600">Edit</a>
          <form action="{{ route('admin.documents.destroy',$doc) }}" method="POST" class="inline" onsubmit="return confirm('Hapus dokumen ini?')">
            @csrf @method('DELETE')
            <button class="px-2 text-red-600">Hapus</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $docs->links() }}</div>
@endsection
