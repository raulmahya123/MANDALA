<!doctype html>
<html lang="id" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Mandala Portal' }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  @stack('head')
</head>
<body class="h-full bg-slate-50 text-slate-800">
  <header class="bg-white border-b sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
      {{-- Brand --}}
      <a href="{{ route('home') }}" class="font-semibold tracking-wide flex items-center gap-2">
        <span class="inline-flex h-7 w-7 rounded-xl bg-gradient-to-br from-emerald-500 via-lime-400 to-cyan-400"></span>
        <span class="hidden sm:inline">MANDALA</span>
      </a>

      {{-- Nav kiri (utama) --}}
      <nav class="hidden md:flex items-center gap-4 text-sm">
        <a href="{{ route('home') }}"
           class="{{ request()->routeIs('home') ? 'text-emerald-600 font-medium' : 'hover:text-emerald-600' }}">
          Browse
        </a>

        {{-- OPsional: daftar form publik (kalau punya route forms.index) --}}
        @if(Route::has('forms.index'))
          <a href="{{ route('forms.index') }}"
             class="{{ request()->routeIs('forms.*') ? 'text-emerald-600 font-medium' : 'hover:text-emerald-600' }}">
            Forms
          </a>
        @endif

        {{-- Entri saya (butuh route user entries; contoh: form.entry.show list, atau dashboard user) --}}
        @auth
          @if(Route::has('user.entries.index'))
            <a href="{{ route('user.entries.index') }}"
               class="{{ request()->routeIs('user.entries.*') ? 'text-emerald-600 font-medium' : 'hover:text-emerald-600' }}">
              Entri Saya
            </a>
          @endif
        @endauth

        {{-- Admin (hanya admin/super_admin) --}}
        @auth
          @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'admin')
            <a href="{{ route('admin.documents.index') }}"
               class="{{ request()->is('admin*') ? 'text-emerald-600 font-medium' : 'hover:text-emerald-600' }}">
              Admin
            </a>
          @endif
        @endauth
      </nav>

      {{-- Nav kanan (auth) --}}
      <div class="flex items-center gap-3">
        {{-- Actions slot per halaman (opsional) --}}
        @hasSection('page_actions')
          <div class="hidden sm:block">@yield('page_actions')</div>
        @endif

        @auth
          {{-- Dropdown user sederhana --}}
          <details class="relative">
            <summary class="list-none cursor-pointer flex items-center gap-2 px-2 py-1.5 rounded-xl border bg-white hover:bg-slate-50">
              <img src="https://api.dicebear.com/8.x/identicon/svg?seed={{ urlencode(auth()->user()->email) }}"
                   class="h-7 w-7 rounded-lg" alt="avatar">
              <span class="hidden sm:inline text-sm text-slate-600">Hi, {{ auth()->user()->name }}</span>
              <svg class="w-4 h-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.6" stroke-linecap="round" d="m6 9 6 6 6-6"/></svg>
            </summary>
            <div class="absolute right-0 mt-2 w-56 rounded-2xl border bg-white shadow-lg overflow-hidden z-50">
              <div class="px-4 py-3 text-sm">
                <div class="font-medium">{{ auth()->user()->name }}</div>
                <div class="text-xs text-slate-500">{{ auth()->user()->email }}</div>
              </div>
              <div class="border-t"></div>

              @if(Route::has('user.entries.index'))
              <a href="{{ route('user.entries.index') }}" class="block px-4 py-2 text-sm hover:bg-slate-50">Entri Saya</a>
              @endif

              <a href="{{ route('home') }}" class="block px-4 py-2 text-sm hover:bg-slate-50">Beranda</a>

              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full text-left px-4 py-2 text-sm hover:bg-slate-50">Logout</button>
              </form>
            </div>
          </details>
        @else
          {{-- Guest --}}
          @if (Route::has('register'))
            <a href="{{ route('register') }}"
               class="hidden sm:inline px-3 py-1 rounded-xl ring-1 ring-inset ring-gray-300 bg-white hover:bg-gray-50">
              Register
            </a>
          @endif
          <a href="{{ route('login') }}"
             class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">
            Login
          </a>
        @endauth
      </div>
    </div>

    {{-- Nav responsive (mobile) --}}
    <div class="md:hidden border-t">
      <div class="max-w-7xl mx-auto px-4 py-2 flex items-center gap-4 text-sm">
        <a href="{{ route('home') }}"
           class="{{ request()->routeIs('home') ? 'text-emerald-600 font-medium' : 'hover:text-emerald-600' }}">
          Browse
        </a>
        @if(Route::has('forms.index'))
          <a href="{{ route('forms.index') }}"
             class="{{ request()->routeIs('forms.*') ? 'text-emerald-600 font-medium' : 'hover:text-emerald-600' }}">
            Forms
          </a>
        @endif
        @auth
          @if(Route::has('user.entries.index'))
            <a href="{{ route('user.entries.index') }}"
               class="{{ request()->routeIs('user.entries.*') ? 'text-emerald-600 font-medium' : 'hover:text-emerald-600' }}">
              Entri Saya
            </a>
          @endif
          @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'admin')
            <a href="{{ route('admin.documents.index') }}"
               class="{{ request()->is('admin*') ? 'text-emerald-600 font-medium' : 'hover:text-emerald-600' }}">
              Admin
            </a>
          @endif
        @endauth
      </div>
    </div>
  </header>

  <main class="max-w-7xl mx-auto px-4 py-6">
    @include('partials.alert')
    {{-- Judul halaman + breadcrumb opsional --}}
    @hasSection('page_title')
      <div class="mb-4">
        <h1 class="text-2xl font-extrabold tracking-tight">@yield('page_title')</h1>
        @hasSection('breadcrumb')
          <div class="mt-1 text-sm text-slate-500">@yield('breadcrumb')</div>
        @endif
      </div>
    @endif

    {{-- Konten --}}
    {{ $slot ?? '' }}
    @yield('content')
  </main>

  @stack('scripts')
</body>
</html>
