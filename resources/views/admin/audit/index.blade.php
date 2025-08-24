@extends('layouts.admin')

@section('page_title','Audit Logs')

@section('admin')
<div class="rounded-xl border bg-white dark:bg-slate-900 p-4">
  <h2 class="text-lg font-semibold mb-4">Audit Logs</h2>

  <table class="w-full text-sm">
    <thead>
      <tr class="border-b text-left">
        <th class="px-2 py-1">Waktu</th>
        <th class="px-2 py-1">User</th>
        <th class="px-2 py-1">Aksi</th>
        <th class="px-2 py-1">Objek</th>
        <th class="px-2 py-1">Detail</th>
      </tr>
    </thead>
    <tbody>
      @foreach($logs as $log)
        <tr class="border-b hover:bg-slate-50">
          <td class="px-2 py-1">{{ $log->created_at->format('Y-m-d H:i') }}</td>
          <td class="px-2 py-1">{{ $log->user?->name ?? '—' }}</td>
          <td class="px-2 py-1">{{ $log->action }}</td>
          <td class="px-2 py-1">{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</td>
          <td class="px-2 py-1">
            <a href="{{ route('admin.audit.show',$log) }}" class="text-emerald-600 hover:underline">Lihat</a>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <div class="mt-4">{{ $logs->links() }}</div>
</div>
@endsection
