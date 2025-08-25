@extends('layouts.admin')

@section('page_title', 'Access Control — '.$department->name)

@section('admin')
<div class="rounded-2xl border bg-white dark:bg-slate-900 p-4">
  <h2 class="text-lg font-semibold mb-4">Kelola Akses: {{ $department->name }}</h2>

  {{-- Form tambah akses --}}
  <form method="POST" action="{{ route('admin.departments.access.store', $department) }}" class="grid md:grid-cols-4 gap-4 mb-6">
    @csrf
    <div>
      <label class="block text-sm font-medium mb-1">User</label>
      <select name="user_id" class="w-full rounded-lg border px-3 py-2" required>
        <option value="">Pilih user…</option>
        @foreach($users as $u)
          <option value="{{ $u->id }}" @selected(old('user_id')==$u->id)>{{ $u->name }} — {{ $u->email }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Scope</label>
      <select name="scope_type" id="scope_type" class="w-full rounded-lg border px-3 py-2" required>
        <option value="department" @selected(old('scope_type')==='department')>Department</option>
        <option value="doc_type" @selected(old('scope_type')==='doc_type')>Doc Type</option>
        <option value="item" @selected(old('scope_type')==='item')>Item</option>
      </select>
    </div>
    <div>
      <label class="block text-sm font-medium mb-1">Scope Detail</label>
      <select name="scope_id" class="w-full rounded-lg border px-3 py-2">
        <option value="">—</option>
        @foreach($docTypes as $dt)
          <option value="{{ $dt->id }}" data-scope="doc_type">{{ $dt->name }}</option>
        @endforeach
        @foreach($items as $it)
          <option value="{{ $it->id }}" data-scope="item">{{ $it->docType->name }} — {{ $it->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="flex items-center">
      <label class="inline-flex items-center space-x-2">
        <input type="checkbox" name="can_edit" value="1" @checked(old('can_edit'))>
        <span>Bisa Edit</span>
      </label>
    </div>
    <div class="md:col-span-4">
      <button class="px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700">Tambah</button>
    </div>
  </form>

  {{-- Daftar akses --}}
  <div class="overflow-x-auto">
    <table class="w-full text-sm border">
      <thead class="bg-slate-100 dark:bg-slate-800">
        <tr>
          <th class="px-3 py-2 text-left">User</th>
          <th class="px-3 py-2">Scope</th>
          <th class="px-3 py-2">Detail</th>
          <th class="px-3 py-2">Hak Edit</th>
          <th class="px-3 py-2"></th>
        </tr>
      </thead>
      <tbody>
        @forelse($accesses as $a)
        <tr class="border-t">
          <td class="px-3 py-2">{{ $a->user->name }} <br><span class="text-xs text-slate-500">{{ $a->user->email }}</span></td>
          <td class="px-3 py-2">{{ ucfirst($a->scope_type) }}</td>
          <td class="px-3 py-2">
            @if($a->scope_type === 'doc_type')
              {{ optional($docTypes->firstWhere('id',$a->scope_id))->name }}
            @elseif($a->scope_type === 'item')
              {{ optional($items->firstWhere('id',$a->scope_id))->name }}
            @else
              —
            @endif
          </td>
          <td class="px-3 py-2 text-center">
            @if($a->can_edit)
              <span class="text-emerald-600 font-medium">Ya</span>
            @else
              <span class="text-slate-400">Tidak</span>
            @endif
          </td>
          <td class="px-3 py-2 text-right">
            <form method="POST" action="{{ route('admin.departments.access.destroy', [$department,$a]) }}" onsubmit="return confirm('Hapus akses ini?')">
              @csrf
              @method('DELETE')
              <button class="px-2 py-1 text-rose-600 hover:underline">Hapus</button>
            </form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="px-3 py-4 text-center text-slate-500">Belum ada akses</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $accesses->links() }}</div>
</div>
@endsection
