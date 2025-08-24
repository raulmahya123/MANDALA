@extends('layouts.admin')

@section('admin')
<h1 class="text-lg font-semibold mb-4">Daftar Department</h1>

<div class="mb-4">
  <a href="{{ route('admin.departments.create') }}" 
     class="px-3 py-2 rounded bg-blue-600 text-white">
    + Tambah Department
  </a>
</div>

<table class="w-full border text-sm">
  <thead class="bg-slate-100">
    <tr>
      <th class="border px-3 py-2 text-left">#</th>
      <th class="border px-3 py-2 text-left">Nama</th>
      <th class="border px-3 py-2 text-left">Slug</th>
      <th class="border px-3 py-2 text-left">Dibuat</th>
      <th class="border px-3 py-2 text-left">Aksi</th>
    </tr>
  </thead>
  <tbody>
    @forelse($departments as $i => $dept)
    <tr>
      <td class="border px-3 py-2">{{ $departments->firstItem() + $i }}</td>
      <td class="border px-3 py-2">{{ $dept->name }}</td>
      <td class="border px-3 py-2 text-gray-500">{{ $dept->slug }}</td>
      <td class="border px-3 py-2">{{ $dept->created_at->format('d M Y') }}</td>
      <td class="border px-3 py-2 space-x-1">
        <a href="{{ route('admin.departments.edit',$dept) }}" class="px-2 py-1 rounded bg-yellow-500 text-white">Edit</a>
        <form method="POST" action="{{ route('admin.departments.destroy',$dept) }}" class="inline">
          @csrf @method('DELETE')
          <button onclick="return confirm('Hapus department ini?')" 
                  class="px-2 py-1 rounded bg-red-600 text-white">
            Hapus
          </button>
        </form>
      </td>
    </tr>
    @empty
    <tr>
      <td colspan="5" class="border px-3 py-4 text-center text-gray-500">
        Belum ada department
      </td>
    </tr>
    @endforelse
  </tbody>
</table>

<div class="mt-4">
  {{ $departments->links() }}
</div>
@endsection
