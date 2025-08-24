@extends('layouts.admin')

@section('page_title', 'Akses User — '.$department->name)

@section('admin')
<div class="space-y-6">

  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-xl font-semibold">Akses User — {{ $department->name }}</h1>
      <p class="text-xs text-slate-500">Kelola siapa saja yang bisa melihat/mengedit dokumen di departemen ini.</p>
    </div>
    <a href="{{ route('admin.departments.index') }}"
       class="px-3 py-2 rounded-xl ring-1 ring-inset ring-gray-300 bg-white hover:bg-gray-50
              dark:bg-slate-800 dark:text-slate-100 dark:ring-slate-700">
      Kembali
    </a>
  </div>

  {{-- Panel info singkat --}}
  <div class="rounded-2xl border bg-white dark:bg-slate-900 p-4">
    <div class="text-sm text-slate-600 dark:text-slate-300">
      <p class="mb-2">Jenis akses:</p>
      <ul class="list-disc pl-5 space-y-1">
        <li><span class="font-medium">Department</span> — akses untuk semua dokumen di departemen ini.</li>
        <li><span class="font-medium">Doc Type</span> — akses untuk satu tipe dokumen (mis. SOP/IK/Form).</li>
        <li><span class="font-medium">Item</span> — akses spesifik ke satu item.</li>
      </ul>
    </div>
  </div>

  {{-- Form Tambah Grant --}}
  <div class="rounded-2xl border bg-white dark:bg-slate-900 p-4">
    <h2 class="font-semibold mb-3">Tambah Akses</h2>

    @if ($errors->any())
      <div class="mb-3 rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
        <ul class="list-disc pl-5">
          @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('admin.departments.access.store', $department) }}" class="grid md:grid-cols-5 gap-3">
      @csrf

      {{-- User --}}
      <div class="md:col-span-2">
        <label class="block text-sm mb-1">User</label>
        <select name="user_id" class="w-full rounded-xl border px-3 py-2" required>
          <option value="">Pilih…</option>
          @foreach($users as $u)
            <option value="{{ $u->id }}" @selected(old('user_id')==$u->id)>{{ $u->name }} — {{ $u->email }}</option>
          @endforeach
        </select>
      </div>

      {{-- Scope Type --}}
      <div>
        <label class="block text-sm mb-1">Scope</label>
        <select name="scope_type" id="scope_type" class="w-full rounded-xl border px-3 py-2" required>
          <option value="department" @selected(old('scope_type')==='department')>Department</option>
          <option value="doc_type"   @selected(old('scope_type')==='doc_type')>Doc Type</option>
          <option value="item"       @selected(old('scope_type')==='item')>Item</option>
        </select>
      </div>

      {{-- Scope DocType --}}
      <div id="scope_doc_type" class="hidden">
        <label class="block text-sm mb-1">Pilih Doc Type</label>
        <select name="scope_id" class="w-full rounded-xl border px-3 py-2">
          <option value="">Pilih…</option>
          @foreach($docTypes as $t)
            <option value="{{ $t->id }}" @selected(old('scope_type')==='doc_type' && old('scope_id')==$t->id)>{{ $t->name }}</option>
          @endforeach
        </select>
      </div>

      {{-- Scope Item --}}
      <div id="scope_item" class="hidden">
        <label class="block text-sm mb-1">Pilih Item</label>
        <select name="scope_id" class="w-full rounded-xl border px-3 py-2">
          <option value="">Pilih…</option>
          @foreach($items as $it)
            <option value="{{ $it->id }}" @selected(old('scope_type')==='item' && old('scope_id')==$it->id)>
              {{ $it->docType->name ?? 'Tipe?' }} — {{ $it->name }}
            </option>
          @endforeach
        </select>
      </div>

      {{-- Can Edit + Submit --}}
      <div class="flex items-end gap-3">
        <label class="inline-flex items-center gap-2 text-sm">
          <input type="checkbox" name="can_edit" value="1" class="rounded" @checked(old('can_edit', false))>
          boleh edit
        </label>
        <button class="px-3 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">Tambah</button>
      </div>
    </form>
  </div>

  {{-- Daftar Grant --}}
  <div class="rounded-2xl border bg-white dark:bg-slate-900 p-4">
    <h2 class="font-semibold mb-3">Daftar Akses</h2>

    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="text-left border-b">
            <th class="py-2 pr-4">User</th>
            <th class="py-2 pr-4">Scope</th>
            <th class="py-2 pr-4">Can Edit</th>
            <th class="py-2 pr-4">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($access as $g)
            <tr class="border-b">
              <td class="py-2 pr-4">{{ $g->user->name }} — {{ $g->user->email }}</td>
              <td class="py-2 pr-4">
                @switch($g->scope_type)
                  @case('department')
                    <span class="px-2 py-1 rounded bg-slate-100">Department</span>
                    @break
                  @case('doc_type')
                    <span class="px-2 py-1 rounded bg-slate-100">Doc Type</span>
                    <span class="text-slate-500">#{{ $g->scope_id }}</span>
                    @break
                  @case('item')
                    <span class="px-2 py-1 rounded bg-slate-100">Item</span>
                    <span class="text-slate-500">#{{ $g->scope_id }}</span>
                    @break
                @endswitch
              </td>
              <td class="py-2 pr-4">{{ $g->can_edit ? 'Ya' : 'Tidak' }}</td>
              <td class="py-2 pr-4">
                <form method="POST" action="{{ route('admin.departments.access.destroy', [$department, $g]) }}"
                      onsubmit="return confirm('Hapus akses ini?')">
                  @csrf @method('DELETE')
                  <button class="px-2 py-1 rounded bg-rose-600 text-white hover:bg-rose-700">Hapus</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" class="py-4 text-slate-500">Belum ada akses.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">{{ $access->links() }}</div>
  </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const sel = document.getElementById('scope_type');
  const elDoc = document.getElementById('scope_doc_type');
  const elItem = document.getElementById('scope_item');

  function sync() {
    const v = sel.value;
    elDoc.classList.toggle('hidden', v !== 'doc_type');
    elItem.classList.toggle('hidden', v !== 'item');
  }
  sel?.addEventListener('change', sync);
  sync();
});
</script>
@endpush
