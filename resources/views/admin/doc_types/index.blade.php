@extends('layouts.admin')

@section('admin')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-lg font-semibold">Doc Types</h1>
  <a href="{{ route('admin.doc-types.create') }}" class="px-3 py-2 rounded bg-blue-600 text-white">Tambah</a>
</div>

<div class="rounded-xl border overflow-hidden bg-white">
  <table class="min-w-full text-sm">
    <thead class="bg-slate-100 text-left">
      <tr>
        <th class="px-4 py-2">Nama</th>
        <th class="px-4 py-2">Slug</th>
        <th class="px-4 py-2"></th>
      </tr>
    </thead>
    <tbody>
      @foreach($docTypes as $t)
      <tr class="border-t">
        <td class="px-4 py-2 font-medium">{{ $t->name }}</td>
        <td class="px-4 py-2">{{ $t->slug }}</td>
        <td class="px-4 py-2 text-right">
          <a href="{{ route('admin.doc-types.edit',$t) }}" class="px-2 text-blue-600">Edit</a>
          <form method="POST" action="{{ route('admin.doc-types.destroy',$t) }}" class="inline" onsubmit="return confirm('Hapus?')">
            @csrf @method('DELETE')
            <button class="px-2 text-red-600">Hapus</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

<div class="mt-4">
  {{ $docTypes->links() }}
</div>
@endsection
