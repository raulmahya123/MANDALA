@if ($msg = session('ok'))
  <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-700">
    {{ $msg }}
  </div>
@endif
@if ($errors->any())
  <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700">
    <div class="font-semibold mb-1">Ada kesalahan:</div>
    <ul class="list-disc list-inside">
      @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
    </ul>
  </div>
@endif
