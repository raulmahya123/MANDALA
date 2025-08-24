@extends('layouts.admin')

@section('page_title','Departments')

@section('admin')
<div class="rounded-2xl border bg-white dark:bg-slate-900 p-4">
  <div class="flex items-center justify-between mb-4 gap-2">
    <div>
      <h2 class="text-lg font-semibold">Departments</h2>
      <p class="text-xs text-slate-500">Kelola daftar departemen</p>
    </div>
    <a href="{{ route('admin.departments.create') }}"
       class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="1.8" stroke-linecap="round" d="M12 5v14M5 12h14"/>
      </svg>
      Tambah
    </a>
  </div>

  @if(session('ok'))
    <div class="mb-3 rounded-xl border border-emerald-200 text-emerald-800 bg-emerald-50 px-4 py-3">
      {{ session('ok') }}
    </div>
  @endif

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="border-b text-slate-500">
          <th class="py-2 px-3 text-left">#</th>
          <th class="py-2 px-3 text-left">Nama</th>
          <th class="py-2 px-3 text-left">Slug</th>
          <th class="py-2 px-3 text-left">Status</th>
          <th class="py-2 px-3 text-left">Dibuat</th>
          <th class="py-2 px-3 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($departments as $i => $dept)
          <tr class="border-b hover:bg-slate-50/60">
            <td class="py-2 px-3">{{ $departments->firstItem() + $i }}</td>
            <td class="py-2 px-3 font-medium">{{ $dept->name }}</td>
            <td class="py-2 px-3 text-slate-500">{{ $dept->slug }}</td>
            <td class="py-2 px-3">
              @if($dept->is_active)
                <span class="px-2 py-1 rounded-full text-xs bg-emerald-100 text-emerald-700">Aktif</span>
              @else
                <span class="px-2 py-1 rounded-full text-xs bg-gray-200 text-gray-600">Nonaktif</span>
              @endif
            </td>
            <td class="py-2 px-3 text-slate-600">{{ $dept->created_at->format('d M Y') }}</td>
            <td class="py-2 px-3">
              <div class="flex items-center justify-end gap-2">
                <a href="{{ route('admin.departments.edit',$dept) }}"
                   class="px-2 py-1 rounded-lg ring-1 ring-inset ring-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-600 dark:hover:bg-slate-700">
                  Edit
                </a>
                <form method="POST" action="{{ route('admin.departments.destroy',$dept) }}"
                      onsubmit="return confirm('Hapus department ini?')">
                  @csrf @method('DELETE')
                  <button
                    class="px-2 py-1 rounded-lg ring-1 ring-inset ring-red-300 bg-white text-red-600 hover:bg-red-50 dark:bg-slate-800 dark:text-red-400 dark:ring-red-700 dark:hover:bg-slate-700">
                    Hapus
                  </button>
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

  <div class="mt-4">
    {{ $departments->links() }}
  </div>
</div>
@endsection
