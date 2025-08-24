<!doctype html>
<html lang="id" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>{{ $title ?? 'Mandala Portal' }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 text-slate-800">
  <header class="bg-white border-b sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
      <a href="{{ route('home') }}" class="font-semibold tracking-wide">MANDALA</a>
      <nav class="flex items-center gap-4 text-sm">
        <a href="{{ route('home') }}" class="hover:text-blue-600">Browse</a>
        @auth
          @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'admin')
            <a href="{{ route('admin.documents.index') }}" class="hover:text-blue-600">Admin</a>
          @endif
          <span class="text-slate-500">Hi, {{ auth()->user()->name }}</span>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="px-3 py-1 rounded bg-slate-100 hover:bg-slate-200">Logout</button>
          </form>
        @else
          <a href="{{ route('login') }}" class="px-3 py-1 rounded bg-blue-600 text-white">Login</a>
        @endauth
      </nav>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 py-6">
    @include('partials.alert')
    {{ $slot ?? '' }}
    @yield('content')
  </main>
</body>
</html>
