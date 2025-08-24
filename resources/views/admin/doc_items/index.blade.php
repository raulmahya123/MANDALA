@extends('layouts.admin')

@section('page_title','Doc Items')

@section('admin')
<div class="rounded-2xl border bg-white dark:bg-slate-900 p-4">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
    <div>
      <h2 class="text-lg font-semibold">Doc Items</h2>
      <p class="text-xs text-slate-500">Daftar item per departemen & doc type</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('admin.doc-items.create') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.8" stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
        Tambah
      </a>
    </div>
  </div>

  @if(session('status')) <div class="mb-3 rounded-xl border border-emerald-200 text-emerald-800 bg-emerald-50 px-4 py-3">{{ session('status') }}</div> @endif

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="border-b text-slate-500">
          <th class="py-2 px-3 text-left">Nama</th>
          <th class="py-2 px-3 text-left">Departemen</th>
          <th class="py-2 px-3 text-left">Doc Type</th>
          <th class="py-2 px-3 text-left">Slug</th>
          <th class="py-2 px-3 text-center">Aktif</th>
          <th class="py-2 px-3 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($items as $it)
          <tr class="border-b hover:bg-slate-50/60">
            <td class="py-2 px-3 font-medium">{{ $it->name }}</td>
            <td class="py-2 px-3">{{ $it->department?->name ?? '-' }}</td>
            <td class="py-2 px-3">{{ $it->docType?->name ?? '-' }}</td>
            <td class="py-2 px-3 text-slate-500">{{ $it->slug }}</td>
            <td class="py-2 px-3 text-center">
              @if($it->is_active)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-emerald-100 text-emerald-700 border border-emerald-200">Aktif</span>
              @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs bg-slate-100 text-slate-700 border">Nonaktif</span>
              @endif
            </td>
            <td class="py-2 px-3">
              <div class="flex items-center justify-end gap-2">
                <a href="{{ route('admin.doc-items.edit',$it) }}" class="px-2 py-1 rounded-lg border hover:bg-slate-100">Edit</a>
                <form method="POST" action="{{ route('admin.doc-items.destroy',$it) }}" onsubmit="return confirm('Hapus item ini?')">
                  @csrf @method('DELETE')
                  <button class="px-2 py-1 rounded-lg border border-red-300 text-red-600 hover:bg-red-50">Hapus</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="py-6 text-center text-slate-500">Belum ada data.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $items->links() }}</div>
</div>
@endsection
