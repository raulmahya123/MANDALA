@extends('layouts.admin')

@section('admin')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-lg font-semibold">Doc Items</h1>
  <a href="{{ route('admin.doc-items.create') }}" class="px-3 py-2 rounded bg-blue-600 text-white">Tambah</a>
</div>
<div class="rounded-xl border overflow-hidden bg-white">
  <table class="min-w-full text-sm">
    <thead class="bg-slate-100 text-left">
      <tr>
        <th class="px-4 py-2">Nama</th>
        <th class="px-4 py-2">Dept</th>
        <th class="px-4 py-2">Type</th>
        <th class="px-4 py-2"></th>
      </tr>
    </thead>
    <tbody>
      @foreach($items as $it)
      <tr class="border-t">
        <td class="px-4 py-2 font-medium">{{ $it->name }}</td>
        <td class="px-4 py-2">{{ $it->department->name }}</td>
        <td class="px-4 py-2">{{ $it->docType->name }}</td>
        <td class="px-4 py-2 text-right">
          <a href="{{ route('admin.doc-items.edit',$it) }}" class="px-2 text-blue-600">Edit</a>
          <form action="{{ route('admin.doc-items.destroy',$it) }}" method="POST" class="inline" onsubmit="return confirm('Hapus item ini?')">
            @csrf @method('DELETE')
            <button class="px-2 text-red-600">Hapus</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $items->links() }}</div>
@endsection
