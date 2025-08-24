@extends('layouts.admin')

@section('admin')
<div class="max-w-xl mx-auto bg-white dark:bg-slate-900 rounded-xl shadow p-6">
    <h1 class="text-lg font-semibold mb-4">Hapus Form</h1>

    <p class="mb-6 text-sm text-slate-600 dark:text-slate-300">
        Apakah kamu yakin ingin menghapus form <span class="font-bold">{{ $form->title }}</span>?
        Tindakan ini tidak bisa dibatalkan.
    </p>

    <form method="POST" action="{{ route('admin.forms.destroy', $form) }}">
        @csrf
        @method('DELETE')

        <div class="flex gap-3">
            <a href="{{ route('admin.forms.index') }}" class="px-4 py-2 rounded bg-slate-200 hover:bg-slate-300">
                Batal
            </a>
            <button type="submit" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">
                Ya, Hapus
            </button>
        </div>
    </form>
</div>
@endsection
