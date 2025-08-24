@extends('layouts.admin')
@section('admin')
<h1 class="text-lg font-semibold mb-4">Edit Form: {{ $form->title }}</h1>

<form method="POST" action="{{ route('admin.forms.update',$form) }}" class="flex items-end gap-3 mb-6">
  @csrf @method('PUT')
  <div>
    <label class="block text-sm mb-1">Judul</label>
    <input name="title" value="{{ $form->title }}" class="rounded border px-3 py-2">
  </div>
  <label class="inline-flex items-center gap-2">
    <input type="checkbox" name="is_active" value="1" {{ $form->is_active?'checked':'' }}> <span>Aktif</span>
  </label>
  <button class="px-3 py-2 rounded bg-blue-600 text-white">Update</button>
  <a class="px-3 py-2 rounded border" href="{{ route('admin.forms.export.excel',$form) }}">Export Excel</a>
  <a class="px-3 py-2 rounded border" href="{{ route('admin.forms.export.pdf',$form) }}">Export PDF</a>
</form>

<div class="grid md:grid-cols-2 gap-6">
  <div class="rounded-xl border bg-white p-4">
    <h2 class="font-semibold mb-2">Field</h2>
    <ul class="text-sm">
      @forelse($form->fields as $f)
        <li class="border-b py-2 flex justify-between">
          <div>#{{ $f->sort_order }} — <b>{{ $f->label }}</b> ({{ $f->name }}) — {{ $f->type }} {{ $f->required? '• required':'' }}</div>
          <form method="POST" action="{{ route('admin.forms.fields.destroy',[$form,$f]) }}" onsubmit="return confirm('Hapus field?')">
            @csrf @method('DELETE')
            <button class="text-red-600">Hapus</button>
          </form>
        </li>
      @empty
        <li>Tidak ada field.</li>
      @endforelse
    </ul>
  </div>

  <div class="rounded-xl border bg-white p-4">
    <h2 class="font-semibold mb-2">Tambah Field</h2>
    <form method="POST" action="{{ route('admin.forms.fields.store',$form) }}" class="grid gap-3 text-sm">
      @csrf
      <input name="label" placeholder="Label" class="rounded border px-3 py-2" required>
      <input name="name" placeholder="Nama (unik, otomatis slug)" class="rounded border px-3 py-2" required>
      <select name="type" class="rounded border px-3 py-2">
        <option>text</option><option>textarea</option><option>number</option><option>date</option><option>select</option><option>checkbox</option>
      </select>
      <textarea name="options" placeholder='Options JSON untuk select/checkbox, contoh: ["A","B"]' class="rounded border px-3 py-2"></textarea>
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="required" value="1"> <span>Required</span>
      </label>
      <input type="number" name="sort_order" value="0" class="rounded border px-3 py-2">
      <button class="px-3 py-2 rounded bg-blue-600 text-white">Tambah</button>
    </form>
  </div>
</div>
@endsection
