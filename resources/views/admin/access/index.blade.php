@extends('layouts.admin')

@section('admin')
<h1 class="text-lg font-semibold mb-4">Akses User — {{ $department->name }}</h1>

<div class="rounded-xl border bg-white p-4 mb-6">
  <form method="POST" action="{{ route('admin.departments.access.store',$department) }}" class="grid md:grid-cols-4 gap-4 text-sm">
    @csrf
    <div>
      <label class="block mb-1">User</label>
      <select name="user_id" class="w-full rounded border px-3 py-2" required>
        <option value="">Pilih...</option>
        @foreach($users as $u)
          <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->email }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block mb-1">Scope Type</label>
      <select name="scope_type" id="scope_type" class="w-full rounded border px-3 py-2" required>
        <option value="department">Department</option>
        <option value="doc_type">Doc Type</option>
        <option value="item">Item</option>
      </select>
    </div>
    <div>
      <label class="block mb-1">Scope Target</label>
      <select name="scope_id" id="scope_id" class="w-full rounded border px-3 py-2">
        <option value="">(Kosong utk Department)</option>
        <optgroup label="Doc Types">
          @foreach($docTypes as $t)
            <option value="{{ $t->id }}" data-for="doc_type">{{ $t->name }}</option>
          @endforeach
        </optgroup>
        <optgroup label="Items ({{ $department->name }})">
          @foreach($items as $it)
            <option value="{{ $it->id }}" data-for="item">{{ $it->docType->name }} · {{ $it->name }}</option>
          @endforeach
        </optgroup>
      </select>
      <p class="text-xs text-slate-500 mt-1">Biarkan kosong jika scope “department”.</p>
    </div>
    <div class="flex items-end gap-2">
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="can_edit" value="1">
        <span>Can Edit</span>
      </label>
      <button class="ml-auto px-3 py-2 rounded bg-blue-600 text-white">Tambah</button>
    </div>
  </form>
</div>

<div class="rounded-xl border overflow-hidden bg-white">
  <table class="min-w-full text-sm">
    <thead class="bg-slate-100 text-left">
      <tr>
        <th class="px-4 py-2">User</th>
        <th class="px-4 py-2">Scope</th>
        <th class="px-4 py-2">Can Edit</th>
        <th class="px-4 py-2"></th>
      </tr>
    </thead>
    <tbody>
      @forelse($access as $a)
      <tr class="border-t">
        <td class="px-4 py-2">{{ $a->user->name }} <span class="text-xs text-slate-500">({{ $a->user->email }})</span></td>
        <td class="px-4 py-2">
          <span class="px-2 py-1 rounded border bg-slate-50 text-slate-700">{{ strtoupper($a->scope_type) }}</span>
          @if($a->scope_type !== 'department')
            <span class="text-xs text-slate-500 ml-2">ID: {{ $a->scope_id }}</span>
          @endif
        </td>
        <td class="px-4 py-2">{{ $a->can_edit ? 'Ya' : 'Tidak' }}</td>
        <td class="px-4 py-2 text-right">
          <form method="POST" action="{{ route('admin.departments.access.destroy', [$department,$a]) }}" onsubmit="return confirm('Hapus akses ini?')">
            @csrf @method('DELETE')
            <button class="px-2 text-red-600">Hapus</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="4" class="px-4 py-6 text-center text-slate-500">Belum ada akses.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-4">{{ $access->links() }}</div>

<script>
  // Opsi kecil: sembunyikan option yg tidak relevan saat pilih scope_type
  document.getElementById('scope_type').addEventListener('change', function(){
    const st = this.value;
    document.querySelectorAll('#scope_id option').forEach(opt => {
      const forAttr = opt.getAttribute('data-for');
      if (!forAttr) return; // kosong / department
      opt.hidden = (forAttr !== st);
    });
  });
</script>
@endsection
