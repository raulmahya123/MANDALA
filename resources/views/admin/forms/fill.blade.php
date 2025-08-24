@extends('layouts.app', ['title'=>$form->title])
@section('content')
<h1 class="text-xl font-semibold mb-4">{{ $form->title }}</h1>
<form method="POST" action="{{ route('form.submit',$form) }}" class="grid gap-4">
  @csrf
  @foreach($form->fields as $f)
    <div>
      <label class="block text-sm mb-1">{{ $f->label }} @if($f->required)<span class="text-red-600">*</span>@endif</label>
      @if($f->type==='text')
        <input name="{{ $f->name }}" class="w-full rounded border px-3 py-2" @if($f->required) required @endif>
      @elseif($f->type==='textarea')
        <textarea name="{{ $f->name }}" rows="3" class="w-full rounded border px-3 py-2" @if($f->required) required @endif></textarea>
      @elseif($f->type==='number')
        <input type="number" name="{{ $f->name }}" class="w-full rounded border px-3 py-2" @if($f->required) required @endif>
      @elseif($f->type==='date')
        <input type="date" name="{{ $f->name }}" class="w-full rounded border px-3 py-2" @if($f->required) required @endif>
      @elseif($f->type==='select')
        @php $opts = $f->options ?? []; @endphp
        <select name="{{ $f->name }}" class="w-full rounded border px-3 py-2" @if($f->required) required @endif>
          <option value="">Pilih...</option>
          @foreach($opts as $o)<option value="{{ $o }}">{{ $o }}</option>@endforeach
        </select>
      @elseif($f->type==='checkbox')
        @php $opts = $f->options ?? []; @endphp
        <div class="flex flex-wrap gap-3">
          @foreach($opts as $o)
          <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="{{ $f->name }}[]" value="{{ $o }}"> <span>{{ $o }}</span>
          </label>
          @endforeach
        </div>
      @endif
    </div>
  @endforeach

  <div class="flex gap-2">
    <button name="draft" value="1" class="px-4 py-2 rounded border">Simpan Draft</button>
    <button name="submit" value="1" class="px-4 py-2 rounded bg-blue-600 text-white">Kirim</button>
  </div>
</form>
@endsection
