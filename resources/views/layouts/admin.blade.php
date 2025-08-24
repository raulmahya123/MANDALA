@extends('layouts.app')

@section('content')
<div class="grid grid-cols-12 gap-6">
  <aside class="col-span-12 lg:col-span-3">
    <div class="rounded-xl border bg-white">
      <div class="p-4 font-semibold border-b">Admin Panel</div>
      <nav class="p-2 text-sm">
        @can('viewAny', App\Models\Department::class) {{-- opsional pakai policy --}}
        <a href="{{ route('admin.departments.index') }}" class="block px-3 py-2 rounded hover:bg-slate-100">Departments</a>
        @endcan
        @if(auth()->user()->role==='super_admin')
        <a href="{{ route('admin.doc-types.index') }}" class="block px-3 py-2 rounded hover:bg-slate-100">Doc Types</a>
        @endif
        <a href="{{ route('admin.doc-items.index') }}" class="block px-3 py-2 rounded hover:bg-slate-100">Doc Items</a>
        <a href="{{ route('admin.documents.index') }}" class="block px-3 py-2 rounded hover:bg-slate-100">Documents</a>
      </nav>
    </div>
  </aside>

  <section class="col-span-12 lg:col-span-9">
    @yield('admin')
  </section>
</div>
@endsection
