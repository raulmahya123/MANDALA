@extends('layouts.admin')

@section('admin')
<h1 class="text-lg font-semibold mb-4">Edit Document</h1>
<form method="POST" action="{{ route('admin.documents.update',$document) }}" enctype="multipart/form-data" class="space-y-4">
  @csrf @method('PUT')
  <div>
    <label class="block text-sm mb-1">Judul</label>
    <input type="text" name="title" value="{{ old('title',$document->title) }}" class="w-full rounded border px-3 py-2" required>
  </div>
  <div>
    <label class="block text-sm mb-1">Ringkasan</label>
    <textarea name="summary" rows="3" class="w-full rounded border px-3 py-2">{{ old('summary',$document->summary) }}</textarea>
  </div>
  <div class="grid md:grid-cols-3 gap-4">
    <div>
      <label class="block text-sm mb-1">Status</label>
      <select name="status" class="w-full rounded border px-3 py-2">
        @foreach(['draft','open','archived'] as $s)
          <option value="{{ $s }}" @selected($document->status===$s)>{{ ucfirst($s) }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-sm mb-1">Published At</label>
      <input type="datetime-local" name="published_at"
             value="{{ optional($document->published_at)->format('Y-m-d\TH:i') }}"
             class="w-full rounded border px-3 py-2">
    </div>
    <div>
      <label class="block text-sm mb-1">Ganti File (opsional)</label>
      <input type="file" name="file" class="w-full rounded border px-3 py-2">
      <div class="text-xs text-slate-500 mt-1">File saat ini: {{ $document->file_ext }}</div>
    </div>
  </div>

  <div class="flex gap-2">
    <a href="{{ route('admin.documents.index') }}" class="px-3 py-2 rounded border">Kembali</a>
    <button class="px-3 py-2 rounded bg-blue-600 text-white">Update</button>
  </div>
</form>
@endsection
