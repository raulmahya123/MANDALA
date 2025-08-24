@extends('layouts.admin')

@section('page_title','Edit Doc Item')

@section('admin')
<div class="rounded-2xl border bg-white dark:bg-slate-900 p-4 max-w-2xl">
  <h2 class="text-lg font-semibold mb-4">Edit Doc Item</h2>

  @if($errors->any())
    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 text-red-700 px-4 py-3">
      <ul class="list-disc pl-4">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.doc-items.update',$docItem) }}" class="grid gap-4 md:grid-cols-2">
    @csrf @method('PUT')

    <div class="md:col-span-1">
      <label class="block text-sm mb-1">Departemen</label>
      <select name="department_id" class="w-full rounded-xl border px-3 py-2" required>
        @foreach($departments as $d)
          <option value="{{ $d->id }}" @selected(old('department_id',$docItem->department_id)==$d->id)>{{ $d->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="md:col-span-1">
      <label class="block text-sm mb-1">Doc Type</label>
      <select name="doc_type_id" class="w-full rounded-xl border px-3 py-2" required>
        @foreach($docTypes as $t)
          <option value="{{ $t->id }}" @selected(old('doc_type_id',$docItem->doc_type_id)==$t->id)>{{ $t->name }}</option>
        @endforeach
      </select>
    </div>

    <div class="md:col-span-2">
      <label class="block text-sm mb-1">Nama Item</label>
      <input type="text" name="name" value="{{ old('name',$docItem->name) }}" class="w-full rounded-xl border px-3 py-2" required>
    </div>

    <div class="md:col-span-2 flex items-center gap-2">
      <a href="{{ route('admin.doc-items.index') }}" class="px-3 py-2 rounded-xl border">Batal</a>
      <button class="px-3 py-2 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">Update</button>
    </div>
  </form>
</div>
@endsection
