@extends('layouts.app', ['title'=>'Form Entry #'.$entry->id])
@section('content')
<h1 class="text-xl font-semibold mb-2">{{ $form->title }} — Entry #{{ $entry->id }}</h1>
<div class="text-sm text-slate-500 mb-4">Status: <b>{{ strtoupper($entry->status) }}</b></div>

<div class="rounded-xl border bg-white">
  <table class="min-w-full text-sm">
    <tbody>
      @foreach($form->fields as $f)
      @php $val = optional($entry->values->firstWhere('form_field_id',$f->id))->value; @endphp
      <tr class="border-t">
        <td class="px-4 py-2 w-1/3">{{ $f->label }}</td>
        <td class="px-4 py-2">{!! $f->type==='checkbox' ? implode(', ', json_decode($val ?? '[]',true) ?? []) : e($val) !!}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>

@if($entry->approvals->count())
  <div class="rounded-xl border bg-white mt-4 p-4 text-sm">
    <div class="font-semibold mb-2">Approval Trail</div>
    <ul class="space-y-1">
      @foreach($entry->approvals as $ap)
      <li>• {{ strtoupper($ap->action) }} oleh {{ $ap->reviewer->name }} — <span class="text-slate-500">{{ $ap->created_at->format('d M Y H:i') }}</span> @if($ap->notes) — “{{ $ap->notes }}” @endif</li>
      @endforeach
    </ul>
  </div>
@endif
@endsection
