@extends('layouts.admin')

@section('admin')
<div class="flex items-center justify-between mb-4">
  <h1 class="text-lg font-semibold">Doc Items</h1>
  <a href="{{ route('admin.doc-items.create') }}" class="px-3 py-2 rounded bg-blue-600 text-white">Tambah</a>
</div>

@if($items->count() === 0)
  <div class="rounded-xl border bg-white p-6 text-center text-slate-600">
    Belum ada Doc Item. Klik <a class="text-blue-600 underline" href="{{ route('admin.doc-items.create') }}">Tambah</a> untuk membuat.
  </div>
@else
  <div class="rounded-xl border overflow-hidden bg-white">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-100 text-left">
        <tr>
          <th class="px-4 py-2">Nama</th>
          <th class="px-4 py-2">Departemen</th>
          <th class="px-4 py-2">Doc Type</th>
          <th class="px-4 py-2">Slug</th>
          <th class="px-4 py-2 w-32 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($items as $item)
          <tr class="border-t">
            <td class="px-4 py-2 font-medium">{{ $item->name }}</td>
            <td class="px-4 py-2">{{ $item->department?->name ?? '-' }}</td>
            <td class="px-4 py-2">{{ $item->docType?->name ?? '-' }}</td>
            <td class="px-4 py-2 text-xs text-slate-600">{{ $item->slug }}</td>
            <td class="px-4 py-2 text-right">
              <a href="{{ route('admin.doc-items.edit',$item) }}" class="px-2 text-blue-600 hover:underline">Edit</a>
              <form action="{{ route('admin.doc-items.destroy',$item) }}" method="POST" class="inline"
                    onsubmit="return confirm('Hapus Doc Item ini?')">
                @csrf
                @method('DELETE')
                <button class="px-2 text-red-600 hover:underline" type="submit">Hapus</button>
              </form>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $items->links() }}
  </div>
@endif
@endsection
