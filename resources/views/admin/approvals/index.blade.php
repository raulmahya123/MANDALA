@extends('layouts.admin')
@section('admin')
<h1 class="text-lg font-semibold mb-4">Menunggu Approval</h1>
<div class="rounded-xl border overflow-hidden bg-white">
  <table class="min-w-full text-sm">
    <thead class="bg-slate-100 text-left">
      <tr><th class="px-4 py-2">Form</th><th class="px-4 py-2">Dept</th><th class="px-4 py-2">User</th><th class="px-4 py-2">Submitted</th><th class="px-4 py-2"></th></tr>
    </thead>
    <tbody>
      @forelse($entries as $e)
      <tr class="border-t">
        <td class="px-4 py-2">{{ $e->form->title }}</td>
        <td class="px-4 py-2">{{ $e->form->department->name }}</td>
        <td class="px-4 py-2">{{ $e->user->name }}</td>
        <td class="px-4 py-2 text-xs">{{ optional($e->submitted_at)->format('d M Y H:i') }}</td>
        <td class="px-4 py-2 text-right">
          <form method="POST" action="{{ route('admin.approvals.decide',$e) }}" class="inline">@csrf
            <input type="hidden" name="action" value="approved">
            <button class="px-2 py-1 rounded bg-emerald-600 text-white">Approve</button>
          </form>
          <form method="POST" action="{{ route('admin.approvals.decide',$e) }}" class="inline">@csrf
            <input type="hidden" name="action" value="rejected">
            <button class="px-2 py-1 rounded bg-red-600 text-white">Reject</button>
          </form>
        </td>
      </tr>
      @empty
      <tr><td colspan="5" class="px-4 py-6 text-center text-slate-500">Tidak ada antrian.</td></tr>
      @endforelse
    </tbody>
  </table>
</div>
<div class="mt-4">{{ $entries->links() }}</div>
@endsection
