{{-- resources/views/admin/forms/index.blade.php --}}
@extends('layouts.admin')

@section('page_title','Forms')

@section('page_actions')
  <a href="{{ route('admin.forms.create') }}"
     class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium">
    + Buat Form Baru
  </a>
@endsection

@section('admin')
<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-slate-50 dark:bg-slate-800/50">
      <tr>
        <th class="px-4 py-3 text-left font-semibold">Judul</th>
        <th class="px-4 py-3 text-left font-semibold">Department</th>
        <th class="px-4 py-3 text-left font-semibold">Doc Type</th>
        <th class="px-4 py-3 text-left font-semibold">Item</th>
        <th class="px-4 py-3 text-left font-semibold">Aktif</th>
        <th class="px-4 py-3 text-left font-semibold w-32">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse($forms as $form)
      <tr class="border-t border-slate-200 dark:border-slate-800">
        <td class="px-4 py-2">{{ $form->title }}</td>
        <td class="px-4 py-2">{{ $form->department->name ?? '-' }}</td>
        <td class="px-4 py-2">{{ $form->docType->name ?? '-' }}</td>
        <td class="px-4 py-2">{{ $form->item->name ?? '-' }}</td>
        <td class="px-4 py-2">
          @if($form->is_active)
            <span class="text-emerald-600 font-medium">Ya</span>
          @else
            <span class="text-slate-400">Tidak</span>
          @endif
        </td>
        <td class="px-4 py-2">
          <a href="{{ route('admin.forms.edit',$form) }}"
             class="text-emerald-600 hover:underline">Edit</a>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="6" class="px-4 py-6 text-center text-slate-500">Belum ada Form.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">
  {{ $forms->links() }}
</div>
@endsection
