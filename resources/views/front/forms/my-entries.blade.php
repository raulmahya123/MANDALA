@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">
  <h1 class="text-xl font-semibold mb-4">Entri Saya</h1>

  @if($entries->isEmpty())
    <div class="rounded-xl border bg-white p-6 text-slate-500">Belum ada entri.</div>
  @else
    <div class="rounded-xl border bg-white">
      <table class="w-full text-sm">
        <thead class="bg-slate-50">
          <tr>
            <th class="text-left p-3">Form</th>
            <th class="text-left p-3">Status</th>
            <th class="text-left p-3">Dibuat</th>
            <th class="p-3"></th>
          </tr>
        </thead>
        <tbody>
        @foreach($entries as $e)
          <tr class="border-t">
            <td class="p-3">{{ $e->form->title ?? '—' }}</td>
            <td class="p-3">{{ ucfirst($e->status) }}</td>
            <td class="p-3">{{ $e->created_at?->format('d M Y H:i') }}</td>
            <td class="p-3 text-right">
              <a href="{{ route('form.entry.show',$e) }}" class="text-emerald-700 hover:underline">Lihat</a>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-4">{{ $entries->links() }}</div>
  @endif
</div>
@endsection
