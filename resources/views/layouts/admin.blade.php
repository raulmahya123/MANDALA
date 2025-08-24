<!doctype html>
<html lang="id"
  x-data="{
    sidebarOpen:false,
    collapsed: JSON.parse(localStorage.getItem('collapsed') ?? 'false'),
    dark: localStorage.getItem('theme')==='dark',
    toggleDark(){ this.dark=!this.dark; localStorage.setItem('theme', this.dark?'dark':'light'); document.documentElement.classList.toggle('dark', this.dark) },
    toggleCollapse(){ this.collapsed=!this.collapsed; localStorage.setItem('collapsed', JSON.stringify(this.collapsed)) }
  }"
  x-init="document.documentElement.classList.toggle('dark', dark)"
  class="scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Admin') — {{ config('app.name') }}</title>

  @vite(['resources/css/app.css','resources/js/app.js'])
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    /* collapse */
    #sidebar[data-collapsed="true"] { width: 4.5rem; }
    #sidebar[data-collapsed="true"] .nav-label, #sidebar[data-collapsed="true"] .nav-divider, #sidebar[data-collapsed="true"] .brand-text { display:none }
    #sidebar[data-collapsed="true"] .nav-link { justify-content:center; padding-left:.5rem; padding-right:.5rem }
    /* active pill */
    .nav-active { position:relative; background:rgba(16,185,129,.12) }
    .nav-active::before { content:""; position:absolute; left:-12px; top:10px; bottom:10px; width:4px; border-radius:9999px; background:linear-gradient(180deg,#10b981,#22c55e) }
    /* glass */
    .glass { backdrop-filter:saturate(160%) blur(8px) }
  </style>
</head>
<body class="bg-gradient-to-b from-slate-50 to-slate-100 dark:from-slate-950 dark:to-slate-900 text-slate-800 dark:text-slate-100">

  <!-- Topbar: MOBILE ONLY -->
  <div class="lg:hidden sticky top-0 z-50 glass bg-white/70 dark:bg-slate-900/70 border-b border-slate-200/70 dark:border-slate-800">
    <div class="h-14 flex items-center justify-between px-3">
      <!-- Hamburger: mobile only -->
      <button
        @click="sidebarOpen=true"
        class="lg:hidden p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800"
        aria-label="Menu Mobile">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-width="1.6" stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>

      <div class="font-semibold">{{ config('app.name') }}</div>

      <button @click="toggleDark()" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Tema">
        <template x-if="!dark"><svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12Z"/></svg></template>
        <template x-if="dark"><svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/></svg></template>
      </button>
    </div>
  </div>

  <div class="min-h-screen flex">

    <!-- Sidebar -->
    <aside id="sidebar"
      :data-collapsed="collapsed"
      class="fixed lg:static inset-y-0 left-0 z-40 w-80 lg:w-80 shrink-0 transition-all duration-200
             bg-white/80 dark:bg-slate-950/80 glass border-r border-slate-200/60 dark:border-slate-800"
      :class="{'-translate-x-full lg:translate-x-0': !sidebarOpen, 'translate-x-0': sidebarOpen, 'lg:w-18': collapsed}"
      @keydown.escape.window="sidebarOpen=false" @click.away="sidebarOpen=false">

      <!-- Brand (desktop) — TANPA tombol collapse -->
      <div class="hidden lg:flex items-center justify-between h-16 px-4 border-b border-slate-200/60 dark:border-slate-800">
        <div class="flex items-center gap-3">
          <div class="h-9 w-9 rounded-2xl shadow-inner bg-gradient-to-br from-emerald-500 via-lime-400 to-cyan-400"></div>
          <div class="brand-text leading-tight">
            <div class="font-extrabold tracking-tight">Admin Panel</div>
            <div class="text-[10px] uppercase text-slate-500">for {{ config('app.name') }}</div>
          </div>
        </div>
        <!-- (hapus tombol di sini) -->
      </div>

      <!-- Brand (mobile) -->
      <div class="lg:hidden flex items-center justify-between h-16 px-4 border-b border-slate-200/60 dark:border-slate-800">
        <div class="flex items-center gap-3">
          <div class="h-9 w-9 rounded-2xl bg-gradient-to-br from-emerald-500 via-lime-400 to-cyan-400"></div>
          <div class="brand-text font-semibold">Admin Panel</div>
        </div>
        <button @click="sidebarOpen=false" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Tutup">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="1.6" stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <!-- Nav -->
      <nav class="px-2 py-3 text-sm">
        <div class="nav-divider px-3 text-[11px] font-semibold tracking-wide text-slate-500 dark:text-slate-400 mb-2 uppercase">Manajemen</div>

        @can('viewAny', App\Models\Department::class)
        <a href="{{ route('admin.departments.index') }}"
           class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('admin.departments.*') ? 'nav-active' : '' }}">
          <svg class="w-5 h-5 opacity-80 group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.6" stroke-linecap="round" d="M3 7h18M6 7v10m12-10v10M3 17h18"/></svg>
          <span class="nav-label">Departments</span>
        </a>
        @endcan

        @if(auth()->user()?->role==='super_admin')
        <a href="{{ route('admin.doc-types.index') }}"
           class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('admin.doc-types.*') ? 'nav-active' : '' }}">
          <svg class="w-5 h-5 opacity-80 group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.6" stroke-linecap="round" d="M4 7h16M4 12h16M4 17h10"/></svg>
          <span class="nav-label">Doc Types</span>
        </a>
        @endif

        <a href="{{ route('admin.doc-items.index') }}"
           class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('admin.doc-items.*') ? 'nav-active' : '' }}">
          <svg class="w-5 h-5 opacity-80 group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.6" stroke-linecap="round" d="M8 6h8M8 12h8M8 18h5"/></svg>
          <span class="nav-label">Doc Items</span>
        </a>

        <a href="{{ route('admin.documents.index') }}"
           class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('admin.documents.*') ? 'nav-active' : '' }}">
          <svg class="w-5 h-5 opacity-80 group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.6" stroke-linecap="round" d="M8 4h8l4 4v12a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/><path stroke-width="1.6" d="M16 4v4h4"/></svg>
          <span class="nav-label">Documents</span>
        </a>

        @if(auth()->user()?->role==='super_admin')
        <div class="nav-divider px-3 text-[11px] font-semibold tracking-wide text-slate-500 dark:text-slate-400 mt-4 mb-2 uppercase">Access Control</div>

        <a href="{{ route('admin.departments.index') }}" class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800">
          <svg class="w-5 h-5 opacity-80 group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.6" stroke-linecap="round" d="M16 11V7a4 4 0 0 0-8 0v4M4 11h16v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-8Z"/></svg>
          <span class="nav-label">Kelola Akses</span>
        </a>

        <a href="{{ route('admin.departments.index') }}" class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800">
          <svg class="w-5 h-5 opacity-80 group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.6" stroke-linecap="round" d="M17 20h5M19.5 17.5V22.5M4 17a4 4 0 1 1 8 0v3H4v-3ZM8 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z"/></svg>
          <span class="nav-label">Kelola Admin Departemen</span>
        </a>
        @endif

        <div class="mt-5 px-3">
          <a href="{{ route('home') }}" class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800">
            <svg class="w-5 h-5 opacity-80 group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.6" stroke-linecap="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-width="1.6" stroke-linecap="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7Z"/></svg>
            <span class="nav-label">Lihat Situs</span>
          </a>
        </div>
      </nav>

      <!-- Footer mini -->
      <div class="mt-auto hidden lg:block p-3 text-[11px] text-slate-500">
        <div class="rounded-xl border border-slate-200/70 dark:border-slate-800 p-3 bg-white/40 dark:bg-slate-900/40">
          <div class="font-semibold mb-1">Shortcut</div>
          <div>Tekan <kbd class="px-1.5 py-0.5 rounded border">/</kbd> untuk cari</div>
        </div>
      </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 min-w-0">

      <!-- Topbar: DESKTOP ONLY (satu-satunya tombol collapse) -->
      <header class="hidden lg:flex sticky top-0 z-30 items-center justify-between h-16 px-6 glass bg-white/70 dark:bg-slate-900/70 border-b border-slate-200/60 dark:border-slate-800">
        <div class="flex items-center gap-3">
          <button
            @click="toggleCollapse()"
            class="hidden lg:flex p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800"
            aria-label="Collapse Sidebar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-width="1.6" stroke-linecap="round" d="m15 19-7-7 7-7"/>
            </svg>
          </button>
          <div class="text-sm text-slate-600 dark:text-slate-400">
            Halo, <span class="font-semibold">{{ auth()->user()->name ?? 'Admin' }}</span>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <form action="#" class="hidden xl:block" onsubmit="return false;">
            <input id="globalSearch" type="search" placeholder="Cari apa saja… (tekan /)"
                   class="w-96 rounded-2xl border-slate-200 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-800/60 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-400">
          </form>

          <button @click="toggleDark()" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Tema">
            <template x-if="!dark">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12Z"/></svg>
            </template>
            <template x-if="dark">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/></svg>
            </template>
          </button>

          @auth
          <div x-data="{open:false}" class="relative">
            <button @click="open=!open" class="flex items-center gap-3 p-1.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800">
              <img src="https://api.dicebear.com/8.x/identicon/svg?seed={{ urlencode(auth()->user()->email) }}" class="h-9 w-9 rounded-xl" alt="avatar">
              <div class="text-left hidden md:block">
                <div class="text-sm font-semibold leading-tight">{{ auth()->user()->name }}</div>
                <div class="text-xs text-slate-500 leading-tight">{{ auth()->user()->email }}</div>
              </div>
              <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="1.6" stroke-linecap="round" d="m6 9 6 6 6-6"/>
              </svg>
            </button>
            <div x-cloak x-show="open" @click.away="open=false" class="absolute right-0 mt-2 w-60 rounded-2xl border bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 shadow-xl overflow-hidden">
              <div class="px-4 py-3">
                <div class="font-medium">{{ auth()->user()->name }}</div>
                <div class="text-xs text-slate-500">{{ auth()->user()->email }}</div>
              </div>
              <div class="border-t border-slate-200 dark:border-slate-800"></div>
              <a href="{{ route('home') }}" class="block px-4 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-800">Lihat Situs</a>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full text-left px-4 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-800">Keluar</button>
              </form>
            </div>
          </div>
          @endauth
        </div>
      </header>

      <!-- Content -->
      <main class="px-4 lg:px-8 py-8">
        <div class="mb-6 flex items-center justify-between">
          <div>
            <h1 class="text-2xl lg:text-3xl font-extrabold tracking-tight">@yield('page_title','Dashboard')</h1>
            @hasSection('breadcrumb')
              <div class="mt-1 text-sm text-slate-500">@yield('breadcrumb')</div>
            @endif
          </div>
          <div class="flex items-center gap-2">
            @yield('page_actions')
          </div>
        </div>

        @if(session('status'))
          <div class="rounded-2xl border border-emerald-200 text-emerald-800 bg-emerald-50 px-4 py-3 mb-6">{{ session('status') }}</div>
        @endif

        <div class="grid gap-6">
          @yield('admin')
        </div>
      </main>

      <!-- Footer -->
      <footer class="px-6 py-6 text-xs text-slate-500 border-t border-slate-200/60 dark:border-slate-800">
        &copy; {{ date('Y') }} {{ config('app.name') }} — Dibuat gagah dengan ❤.
      </footer>
    </div>
  </div>

  @stack('scripts')
</body>
</html>
