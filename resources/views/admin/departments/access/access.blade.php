@extends('layouts.admin')

@section('title', 'Akses User — '.$department->name)

@section('admin')
<div class="space-y-6" 
     x-data="accessUI({
        docTypes: @json($docTypes ?? []),
        items: @json($items ?? []),
     })">

  {{-- Title + Breadcrumb mini --}}
  <div class="flex items-center justify-between gap-3">
    <div>
      <h1 class="text-xl font-semibold">Akses User — {{ $department->name }}</h1>
      <p class="text-sm text-slate-500">Kelola siapa saja yang bisa melihat / mengedit dokumen di departemen ini.</p>
    </div>
  </div>

  {{-- Alert --}}
  @if(session('success'))
    <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm">
      {{ session('success') }}
    </div>
  @endif
  @if($errors->any())
    <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
      <ul class="list-disc ml-5">
        @foreach($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  {{-- Form Tambah Akses --}}
  <div class="rounded-2xl border bg-white dark:bg-slate-900">
    <div class="px-4 py-3 border-b flex items-center justify-between">
      <h2 class="font-medium">Tambah Akses</h2>
    </div>
    <form method="POST" action="{{ route('admin.departments.access.store', $department) }}" class="p-4 grid md:grid-cols-4 gap-4 text-sm">
      @csrf

      {{-- User --}}
      <div class="md:col-span-2">
        <label class="block mb-1 font-medium">User <span class="text-rose-600">*</span></label>
        <select name="user_id" class="w-full rounded-xl border px-3 py-2" required>
          <option value="">— Pilih user —</option>
          @foreach(($users ?? []) as $u)
            <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
          @endforeach
        </select>
        <p class="text-xs text-slate-500 mt-1">User yang dipilih akan diberi akses di departemen ini.</p>
      </div>

      {{-- Scope Type --}}
      <div>
        <label class="block mb-1 font-medium">Scope Akses</label>
        <select name="scope_type" x-model="scopeType" class="w-full rounded-xl border px-3 py-2">
          <option value="department">Department (seluruhnya)</option>
          <option value="doc_type">Per Doc Type</option>
          <option value="item">Per Item</option>
        </select>
      </div>

      {{-- Can Edit --}}
      <div class="flex items-end">
        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="can_edit" value="1" class="rounded border">
          <span class="font-medium">Boleh edit</span>
        </label>
      </div>

      {{-- Doc Type (muncul kalau scope_type = doc_type atau item) --}}
      <div x-show="scopeType !== 'department'" class="md:col-span-2" x-cloak>
        <label class="block mb-1 font-medium">Doc Type</label>
        <select x-model="selectedDocTypeId" class="w-full rounded-xl border px-3 py-2">
          <option value="">— Pilih doc type —</option>
          <template x-for="dt in docTypes" :key="dt.id">
            <option :value="dt.id" x-text="dt.name"></option>
          </template>
        </select>
        <p class="text-xs text-slate-500 mt-1">Wajib saat memilih scope Doc Type atau Item.</p>
      </div>

      {{-- Item (muncul kalau scope_type = item) --}}
      <div x-show="scopeType === 'item'" class="md:col-span-2" x-cloak>
        <label class="block mb-1 font-medium">Item</label>
        <select name="scope_id" class="w-full rounded-xl border px-3 py-2" :disabled="scopeType!=='item'">
          <option value="">— Pilih item —</option>
          {{-- Dirotasi via x-for & filter by selectedDocTypeId --}}
          <template x-for="it in filteredItems()" :key="it.id">
            <option :value="it.id" x-text="displayItem(it)"></option>
          </template>
        </select>
        <p class="text-xs text-slate-500 mt-1">Item difilter berdasar Doc Type yang dipilih.</p>
      </div>

      {{-- Hidden scope_id untuk scope department/doc_type --}}
      <input type="hidden" name="scope_id"
             :value="scopeType==='department' ? '' : (scopeType==='doc_type' ? selectedDocTypeId : '')">

      {{-- Submit --}}
      <div class="md:col-span-4">
        <button class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 text-white px-4 py-2 hover:bg-emerald-700">
          Simpan Akses
        </button>
      </div>
    </form>
  </div>

  {{-- Tabel Akses --}}
  <div class="rounded-2xl border bg-white dark:bg-slate-900">
    <div class="px-4 py-3 border-b flex items-center justify-between">
      <h2 class="font-medium">Daftar Akses</h2>
      {{-- (opsional) quick filter/search bisa ditambah di sini --}}
    </div>

    <div class="overflow-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-slate-50 text-slate-600">
          <tr>
            <th class="text-left px-4 py-2">User</th>
            <th class="text-left px-4 py-2">Scope</th>
            <th class="text-left px-4 py-2">Boleh Edit</th>
            <th class="text-left px-4 py-2">Dibuat</th>
            <th class="px-4 py-2"></th>
          </tr>
        </thead>
        <tbody>
          @forelse(($accesses ?? []) as $a)
            <tr class="border-t">
              <td class="px-4 py-2">
                <div class="font-medium">{{ $a->user->name ?? '-' }}</div>
                <div class="text-xs text-slate-500">{{ $a->user->email ?? '' }}</div>
              </td>
              <td class="px-4 py-2">
                @switch($a->scope_type)
                  @case('department')
                    <span class="inline-flex items-center rounded-lg bg-slate-100 px-2 py-1 text-xs">Department (semua)</span>
                    @break
                  @case('doc_type')
                    <div class="text-sm">
                      Doc Type: <span class="font-medium">
                        {{ optional($a->docType ?? null)->name ?? ('#'.$a->scope_id) }}
                      </span>
                    </div>
                    @break
                  @case('item')
                    <div class="text-sm">
                      Item: <span class="font-medium">
                        {{ optional($a->item ?? null)->name ?? ('#'.$a->scope_id) }}
                      </span>
                    </div>
                    @break
                  @default
                    {{ ucfirst($a->scope_type) }} #{{ $a->scope_id }}
                @endswitch
              </td>
              <td class="px-4 py-2">
                @if($a->can_edit)
                  <span class="inline-flex items-center rounded-lg bg-emerald-100 text-emerald-700 px-2 py-1 text-xs">Ya</span>
                @else
                  <span class="inline-flex items-center rounded-lg bg-slate-100 text-slate-600 px-2 py-1 text-xs">Tidak</span>
                @endif
              </td>
              <td class="px-4 py-2 text-slate-500">{{ $a->created_at?->format('d M Y H:i') }}</td>
              <td class="px-4 py-2 text-right">
                <form method="POST" action="{{ route('admin.departments.access.destroy', [$department, $a->id]) }}"
                      onsubmit="return confirm('Hapus akses ini?')">
                  @csrf
                  @method('DELETE')
                  <button class="inline-flex items-center gap-2 rounded-xl border px-3 py-1.5 hover:bg-slate-50">
                    Hapus
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-4 py-8 text-center text-slate-500">Belum ada akses.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if(method_exists(($accesses ?? null), 'links'))
      <div class="px-4 py-3 border-t">
        {{ $accesses->links() }}
      </div>
    @endif
  </div>
</div>

{{-- Alpine helpers --}}
<script>
function accessUI({docTypes=[], items=[]}){
  return {
    scopeType: 'department',
    selectedDocTypeId: '',
    docTypes,
    items,
    filteredItems(){
      if(!this.selectedDocTypeId) return [];
      return this.items.filter(it => String(it.doc_type_id) === String(this.selectedDocTypeId));
    },
    displayItem(it){
      // tampilkan "Item Name — (Kode/ID)" jika punya kode, sesuaikan field mu
      return it.name ?? ('Item #'+it.id);
    }
  }
}
</script>
@endsection
