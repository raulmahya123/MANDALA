@extends('layouts.admin')

@section('page_title','Documents')

@section('admin')
<div class="rounded-2xl border bg-white dark:bg-slate-900 p-4">
  <div class="flex items-center justify-between mb-4 gap-2">
    <div>
      <h2 class="text-lg font-semibold">Documents</h2>
      <p class="text-xs text-slate-500">Kelola dokumen per departemen, tipe, dan item</p>
    </div>
    <a href="{{ route('admin.documents.create') }}"
       class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <path stroke-width="1.8" stroke-linecap="round" d="M12 5v14M5 12h14"/>
      </svg>
      Tambah
    </a>
  </div>

  {{-- filter bar --}}
  <form method="GET" class="mb-4 grid gap-2 md:grid-cols-3">
    <div class="md:col-span-2">
      <input type="search" name="q" value="{{ request('q') }}"
             placeholder="Cari judul dokumen…"
             class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950
                    px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
    </div>
    <div class="flex items-center gap-2">
      <select name="status"
              class="flex-1 rounded-xl border border-slate-300 dark:border-slate-700 bg-white/90 dark:bg-slate-950
                     px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
        <option value="">Semua status</option>
        @foreach(['draft'=>'Draft','open'=>'Open','archived'=>'Archived'] as $k => $lbl)
          <option value="{{ $k }}" @selected(request('status')===$k)>{{ $lbl }}</option>
        @endforeach
      </select>
      <button class="px-3 py-2 rounded-xl ring-1 ring-inset ring-gray-300 bg-white text-gray-700 hover:bg-gray-50
                     dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-600 dark:hover:bg-slate-700">
        Terapkan
      </button>
      @if(request()->hasAny(['q','status']))
      <a href="{{ route('admin.documents.index') }}"
         class="px-3 py-2 rounded-xl text-sm text-slate-600 hover:underline">Reset</a>
      @endif
    </div>
  </form>

  @if(session('ok'))
    <div class="mb-3 rounded-xl border border-emerald-200 text-emerald-800 bg-emerald-50 px-4 py-3">
      {{ session('ok') }}
    </div>
  @endif

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead>
        <tr class="border-b text-slate-500">
          <th class="py-2 px-3 text-left">Judul</th>
          <th class="py-2 px-3 text-left">Dept / Type / Item</th>
          <th class="py-2 px-3 text-left">Status</th>
          <th class="py-2 px-3 text-left">Publish</th>
          <th class="py-2 px-3 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($docs as $doc)
          <tr class="border-b hover:bg-slate-50/60 dark:hover:bg-slate-800/40">
            <td class="py-2 px-3 font-medium">
              {{ $doc->title }}
            </td>

            <td class="py-2 px-3">
              <div class="text-xs text-slate-600 dark:text-slate-300">
                {{ $doc->department->name }} · {{ $doc->docType->name }} · {{ optional($doc->item)->name ?? '-' }}
              </div>
            </td>

            {{-- workflow status --}}
            <td class="py-2 px-3">
              @php $open = ($doc->status ?? '') === 'open'; @endphp
              <span class="px-2 py-1 rounded-full text-xs
                           {{ $open ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-700' }}">
                {{ strtoupper($doc->status ?? 'DRAFT') }}
              </span>
            </td>

            <td class="py-2 px-3 text-xs text-slate-600 dark:text-slate-300">
              {{ optional($doc->published_at)->format('d M Y') ?? '—' }}
            </td>

            <td class="py-2 px-3">
              <div class="flex items-center justify-end gap-2">
                <a href="{{ route('admin.documents.edit',$doc) }}"
                   class="px-2 py-1 rounded-lg ring-1 ring-inset ring-gray-300 bg-white text-gray-700 hover:bg-gray-50
                          dark:bg-slate-800 dark:text-slate-200 dark:ring-slate-600 dark:hover:bg-slate-700">
                  Edit
                </a>
                <form action="{{ route('admin.documents.destroy',$doc) }}" method="POST"
                      onsubmit="return confirm('Hapus dokumen ini?')">
                  @csrf @method('DELETE')
                  <button
                    class="px-2 py-1 rounded-lg ring-1 ring-inset ring-red-300 bg-white text-red-600 hover:bg-red-50
                           dark:bg-slate-800 dark:text-red-400 dark:ring-red-700 dark:hover:bg-slate-700">
                    Hapus
                  </button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="py-6 text-center text-slate-500">Belum ada dokumen.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $docs->links() }}</div>
</div>
@endsection
