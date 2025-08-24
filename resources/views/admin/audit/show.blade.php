@extends('layouts.admin')

@section('page_title','Detail Audit Log')

@section('admin')
<div class="rounded-xl border bg-white dark:bg-slate-900 p-4">
  <h2 class="text-lg font-semibold mb-4">Detail Audit Log</h2>

  <dl class="space-y-2 text-sm">
    <dt class="font-medium">Waktu</dt>
    <dd>{{ $log->created_at }}</dd>

    <dt class="font-medium">User</dt>
    <dd>{{ $log->user?->name ?? '—' }}</dd>

    <dt class="font-medium">Action</dt>
    <dd>{{ $log->action }}</dd>

    <dt class="font-medium">Auditable</dt>
    <dd>{{ $log->auditable_type }} #{{ $log->auditable_id }}</dd>

    <dt class="font-medium">Meta</dt>
    <dd><pre class="bg-slate-100 dark:bg-slate-800 p-2 rounded text-xs">{{ json_encode($log->meta, JSON_PRETTY_PRINT) }}</pre></dd>

    <dt class="font-medium">IP</dt>
    <dd>{{ $log->ip }}</dd>

    <dt class="font-medium">User Agent</dt>
    <dd class="truncate">{{ $log->user_agent }}</dd>
  </dl>
</div>
@endsection
