<!doctype html>
<html  lang="id"
  x-data="{
    sidebarOpen: false,
    collapsed: JSON.parse(localStorage.getItem('collapsed') ?? 'false'),

    // default: pakai localStorage, kalau belum ada → ikut prefers-color-scheme
    dark: (()=>{
      const s = localStorage.getItem('theme');
      return s ? s === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
    })(),

    toggleDark(){
      this.dark = !this.dark;
      localStorage.setItem('theme', this.dark ? 'dark' : 'light');
      // class html di-sync oleh $watch di x-init
    },

    toggleCollapse(){
      this.collapsed = !this.collapsed;
      localStorage.setItem('collapsed', JSON.stringify(this.collapsed));
    }
  }"
  x-init="
    // set awal
    document.documentElement.classList.toggle('dark', dark);
    // auto-sync setiap dark berubah
    $watch('dark', v => document.documentElement.classList.toggle('dark', v));
  "
  class="scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content=\"{{ csrf_token() }}\">
  <title>@yield('title','Admin') — {{ config('app.name') }}</title>

  @vite(['resources/css/app.css','resources/js/app.js'])
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    /* collapse */
    #sidebar[data-collapsed="true"] { width: 4.5rem; }
    #sidebar[data-collapsed="true"] .nav-label,
    #sidebar[data-collapsed="true"] .nav-divider,
    #sidebar[data-collapsed="true"] .brand-text { display: none }
    #sidebar[data-collapsed="true"] .nav-link { justify-content: center; padding-left: .5rem; padding-right: .5rem }

    /* active pill */
    .nav-active { position: relative; background: rgba(16,185,129,.12) }
    .nav-active::before {
      content:""; position:absolute; left:-12px; top:10px; bottom:10px; width:4px; border-radius:9999px;
      background: linear-gradient(180deg, #10b981, #22c55e);
    }

    /* glass */
    .glass { backdrop-filter: saturate(160%) blur(8px) }

    [x-cloak]{ display:none !important; }
  </style>

  @stack('head')
</head>
<body x-trap.noscroll="sidebarOpen"
      class="bg-gradient-to-b from-slate-50 to-slate-100 dark:from-slate-950 dark:to-slate-900 text-slate-800 dark:text-slate-100">

  <!-- Topbar: MOBILE -->
  <div class="lg:hidden sticky top-0 z-50 glass bg-white/70 dark:bg-slate-900/70 border-b border-slate-200/70 dark:border-slate-800">
    <div class="h-14 flex items-center justify-between px-3">
      <button @click="sidebarOpen=true" class="lg:hidden p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Menu Mobile">
        <!-- bars-3 -->
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.6" stroke-linecap="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
      </button>

      <div class="font-semibold">{{ config('app.name') }}</div>

      <button @click="toggleDark()" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Tema">
        <template x-if="!dark">
          <!-- sun -->
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.6" stroke-linecap="round" d="M12 4.5v-2.25m0 19.5V19.5M19.5 12h2.25M2.25 12H4.5M16.95 7.05l1.59-1.59M5.46 18.54l1.59-1.59m0-9.9L5.46 5.46m12.08 12.08 1.59 1.59"/><circle cx="12" cy="12" r="4.5" /></svg>
        </template>
        <template x-if="dark">
          <!-- moon -->
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.6" stroke-linecap="round" d="M21 12.79A9 9 0 1 1 11.21 3 7.5 7.5 0 0 0 21 12.79Z"/></svg>
        </template>
      </button>
    </div>
  </div>

  <div class="min-h-screen flex">
    <!-- Backdrop (mobile) -->
    <div x-cloak class="lg:hidden fixed inset-0 z-30 bg-black/30 backdrop-blur-sm" x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen=false"></div>

    <!-- Sidebar -->
    <aside id="sidebar"
      x-cloak
      :data-collapsed="collapsed"
      class="fixed lg:static inset-y-0 left-0 z-40 w-80 lg:w-80 shrink-0
             bg-white/80 dark:bg-slate-950/80 glass border-r border-slate-200/60 dark:border-slate-800
             transform-gpu transition-transform duration-200 ease-out"
      :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
      @keydown.escape.window="sidebarOpen=false">

      <!-- Brand (desktop) -->
      <div class="hidden lg:flex items-center justify-between h-16 px-4 border-b border-slate-200/60 dark:border-slate-800">
        <div class="flex items-center gap-3">
          <div class="h-9 w-9 rounded-2xl shadow-inner bg-gradient-to-br from-emerald-500 via-lime-400 to-cyan-400"></div>
          <div class="brand-text leading-tight">
            <div class="font-extrabold tracking-tight">Admin AAP</div>
            <div class="text-[10px] uppercase text-slate-500">for {{ config('app.name') }}</div>
          </div>
        </div>
      </div>

      <!-- Brand (mobile) -->
      <div class="lg:hidden flex items-center justify-between h-16 px-4 border-b border-slate-200/60 dark:border-slate-800">
        <div class="flex items-center gap-3">
          <div class="h-9 w-9 rounded-2xl bg-gradient-to-br from-emerald-500 via-lime-400 to-cyan-400"></div>
          <div class="brand-text font-semibold">Admin Panel</div>
        </div>
        <button @click="sidebarOpen=false" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Tutup">
          <!-- x-mark -->
          <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.6" stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
        </button>
      </div>

      <!-- Nav -->
      <nav class="px-2 py-3 text-sm">
        <div class="nav-divider px-3 text-[11px] font-semibold tracking-wide text-slate-500 dark:text-slate-400 mb-2 uppercase">Manajemen</div>

        @can('viewAny', App\Models\Department::class)
        <a href="{{ route('admin.departments.index') }}"
           @click="sidebarOpen=false"
           class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('admin.departments.*') ? 'nav-active' : '' }}">
          <!-- building-office -->
          <svg class="w-5 h-5 opacity-80 group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="1.6" stroke-linecap="round" d="M3.75 21h16.5M4.5 21V5.25A2.25 2.25 0 0 1 6.75 3h4.5A2.25 2.25 0 0 1 13.5 5.25V21m-9-12h9m-9 4.5h9M9 21v-3.75a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 .75.75V21"/>
          </svg>
          <span class="nav-label">Departments</span>
        </a>
        @endcan

        @if(auth()->user()?->role==='super_admin')
        <a href="{{ route('admin.doc-types.index') }}"
           @click="sidebarOpen=false"
           class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('admin.doc-types.*') ? 'nav-active' : '' }}">
          <!-- squares-2x2 -->
          <svg class="w-5 h-5 opacity-80 group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="1.6" stroke-linecap="round" d="M3.75 4.5h6v6h-6zM14.25 4.5h6v6h-6zM14.25 15h6v6h-6zM3.75 15h6v6h-6z"/>
          </svg>
          <span class="nav-label">Doc Types</span>
        </a>
        @endif

        <a href="{{ route('admin.doc-items.index') }}"
           @click="sidebarOpen=false"
           class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('admin.doc-items.*') ? 'nav-active' : '' }}">
          <!-- list-bullet -->
          <svg class="w-5 h-5 opacity-80 group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="1.6" stroke-linecap="round" d="M8.25 6.75h12M8.25 12h12M8.25 17.25h9M3.75 6.75h.01M3.75 12h.01M3.75 17.25h.01"/>
          </svg>
          <span class="nav-label">Doc Items</span>
        </a>

        <a href="{{ route('admin.documents.index') }}"
           @click="sidebarOpen=false"
           class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('admin.documents.*') ? 'nav-active' : '' }}">
          <!-- document -->
          <svg class="w-5 h-5 opacity-80 group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="1.6" d="M15.75 2.25H8.25A2.25 2.25 0 0 0 6 4.5v15A2.25 2.25 0 0 0 8.25 21h7.5A2.25 2.25 0 0 0 18 18.75V6.75l-2.25-4.5z"/>
            <path stroke-width="1.6" d="M18 6.75h-2.25V2.25"/>
          </svg>
          <span class="nav-label">Documents</span>
        </a>

        <!-- Forms -->
        <a href="{{ route('admin.forms.index') }}"
           @click="sidebarOpen=false"
           class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('admin.forms.*') ? 'nav-active' : '' }}">
          <!-- clipboard-document-list -->
          <svg class="w-5 h-5 opacity-80 group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="1.6" stroke-linecap="round" d="M9 3.75h6M9.75 6h4.5M7.5 6A1.5 1.5 0 0 1 9 4.5h6A1.5 1.5 0 0 1 16.5 6v.75h.75A2.25 2.25 0 0 1 19.5 9v9A2.25 2.25 0 0 1 17.25 20.25H6.75A2.25 2.25 0 0 1 4.5 18V9A2.25 2.25 0 0 1 6.75 6.75H7.5zM8.25 12h7.5M8.25 15.75h7.5"/>
          </svg>
          <span class="nav-label">Forms</span>
        </a>

        <a href="{{ route('admin.audit.index') }}"
           class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 {{ request()->routeIs('admin.audit.*') ? 'nav-active' : '' }}">
          <!-- bars-3-center-left -->
          <svg class="w-5 h-5 opacity-80 group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="1.6" stroke-linecap="round" d="M3.75 6.75h16.5M6.75 12h13.5M9.75 17.25h10.5"/>
          </svg>
          <span class="nav-label">Audit Logs</span>
        </a>

        <a href="{{ route('admin.approvals.index') }}"
           @click="sidebarOpen=false"
           class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('admin.approvals.*') ? 'nav-active' : '' }}">
          <!-- check-badge -->
          <svg class="w-5 h-5 opacity-80 group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="1.6" stroke-linecap="round" d="m9 12 2 2 4-4"/><path stroke-width="1.6" d="M12 2.25l2.036 3.96 4.377.637-3.156 3.08.745 4.352L12 12.75l-3.999 2.529.745-4.352-3.156-3.08 4.377-.637L12 2.25z"/>
          </svg>
          <span class="nav-label">Approvals</span>
        </a>

        @if(auth()->user()?->role==='super_admin')
        <div class="nav-divider px-3 text-[11px] font-semibold tracking-wide text-slate-500 dark:text-slate-400 mt-4 mb-2 uppercase">Access Control</div>
        <a href="{{ route('admin.departments.index') }}"
           @click="sidebarOpen=false"
           class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('admin.departments.create') ? 'nav-active' : '' }}">
          <!-- plus-circle -->
          <svg class="w-5 h-5 opacity-80 group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-width="1.6" stroke-linecap="round" d="M12 6v12m6-6H6"/><circle cx="12" cy="12" r="9" stroke-width="1.6"/>
          </svg>
          <span class="nav-label">Tambah Department</span>
        </a>
        @endif

        {{-- Access Control per Department --}}
        @php
          $u = auth()->user();
          $manageableDepts = collect();
          if ($u) {
            $manageableDepts = $u->role === 'super_admin'
              ? \App\Models\Department::orderBy('name')->get(['id','name','slug'])
              : $u->adminDepartments()->orderBy('name')->get(['id','name','slug']);
          }
        @endphp

        @if($manageableDepts->isNotEmpty())
          <div class="nav-divider px-3 text-[11px] font-semibold tracking-wide text-slate-500 dark:text-slate-400 mt-4 mb-2 uppercase">Access Dept</div>

          <div x-data="{ openAccess: {{ request()->routeIs('admin.departments.access.*') ? 'true' : 'false' }} }" class="px-2">
            <button @click="openAccess=!openAccess"
                    class="w-full nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800"
                    :class="openAccess ? 'nav-active' : ''">
              <!-- queue-list -->
              <svg class="w-5 h-5 opacity-80 group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-width="1.6" stroke-linecap="round" d="M3.75 6.75h16.5M3.75 12h9.75M3.75 17.25h12.75"/>
              </svg>
              <span class="nav-label flex-1 text-left">Access Dept</span>
              <!-- chevron-down -->
              <svg class="w-4 h-4 transition-transform duration-200" :class="openAccess ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-width="1.6" stroke-linecap="round" d="m6 9 6 6 6-6"/>
              </svg>
            </button>

            <div x-cloak x-show="openAccess" x-transition>
              <ul class="mt-2 space-y-1">
                @foreach($manageableDepts->take(12) as $d)
                <li>
                  <a href="{{ route('admin.departments.access.index', $d) }}"
                     @click="sidebarOpen=false"
                     class="ml-9 block px-3 py-2 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800
                     {{ (request()->routeIs('admin.departments.access.*') && (request()->route('department')?->slug === $d->slug)) ? 'bg-emerald-50/70 dark:bg-emerald-900/20' : '' }}">
                    {{ $d->name }}
                  </a>
                </li>
                @endforeach
                @if($manageableDepts->count() > 12)
                <li class="ml-9">
                  <a href="{{ route('admin.departments.index') }}"
                     @click="sidebarOpen=false"
                     class="block px-3 py-2 rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800">
                    Lihat semua…
                  </a>
                </li>
                @endif
              </ul>
            </div>
          </div>
        @elseif(auth()->user()?->role === 'super_admin')
          <div class="nav-divider px-3 text-[11px] font-semibold tracking-wide text-slate-500 dark:text-slate-400 mt-4 mb-2 uppercase">Access Dept</div>
          <a href="{{ route('admin.departments.index') }}"
             @click="sidebarOpen=false"
             class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800">
            <!-- queue-list -->
            <svg class="w-5 h-5 opacity-80 group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-width="1.6" stroke-linecap="round" d="M3.75 6.75h16.5M3.75 12h9.75M3.75 17.25h12.75"/>
            </svg>
            <span class="nav-label">Kelola Akses</span>
          </a>
        @endif

        <div class="mt-5 px-3">
          <a href="{{ route('home') }}" @click="sidebarOpen=false" class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800">
            <!-- globe-alt (lihat situs) -->
            <svg class="w-5 h-5 opacity-80 group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-width="1.6" stroke-linecap="round" d="M12 21a9 9 0 1 0-9-9 9 9 0 0 0 9 9Zm0 0c4.97 0 9-4.03 9-9m-9 9c-4.97 0-9-4.03-9-9m9 9c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3 7.5 7.03 7.5 12 9.515 21 12 21Z"/>
            </svg>
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
      <!-- Topbar: DESKTOP -->
      <header class="hidden lg:flex sticky top-0 z-30 items-center justify-between h-16 px-6 glass bg-white/70 dark:bg-slate-900/70 border-b border-slate-200/60 dark:border-slate-800">
        <div class="flex items-center gap-3">
          <button @click="toggleCollapse" class="hidden lg:flex p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Collapse Sidebar">
            <!-- chevron-left -->
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.6" stroke-linecap="round" d="m15 19-7-7 7-7"/></svg>
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

          <button @click="toggleDark" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800" aria-label="Tema">
            <template x-if="!dark">
              <!-- sun -->
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.6" stroke-linecap="round" d="M12 4.5v-2.25m0 19.5V19.5M19.5 12h2.25M2.25 12H4.5M16.95 7.05l1.59-1.59M5.46 18.54l1.59-1.59m0-9.9L5.46 5.46m12.08 12.08 1.59 1.59"/><circle cx="12" cy="12" r="4.5"/></svg>
            </template>
            <template x-if="dark">
              <!-- moon -->
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.6" stroke-linecap="round" d="M21 12.79A9 9 0 1 1 11.21 3 7.5 7.5 0 0 0 21 12.79Z"/></svg>
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
              <!-- chevron-down -->
              <svg class="w-4 h-4 ml-1" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.6" stroke-linecap="round" d="m6 9 6 6 6-6"/></svg>
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

        {{-- Flash & Errors --}}
        @if(session('ok') || session('status') || session('error'))
          @if(session('ok') || session('status'))
            <div class="rounded-2xl border border-emerald-200 text-emerald-800 bg-emerald-50 px-4 py-3 mb-6">
              {{ session('ok') ?? session('status') }}
            </div>
          @endif
          @if(session('error'))
            <div class="rounded-2xl border border-rose-200 text-rose-800 bg-rose-50 px-4 py-3 mb-6">
              {{ session('error') }}
            </div>
          @endif
        @endif

        @if ($errors->any())
          <div class="rounded-2xl border border-amber-200 text-amber-900 bg-amber-50 px-4 py-3 mb-6">
            <div class="font-semibold mb-1">Periksa kembali input kamu:</div>
            <ul class="list-disc list-inside text-sm space-y-1">
              @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
              @endforeach
            </ul>
          </div>
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

  <script>
    // fokus ke search saat tekan '/'
    window.addEventListener('keydown', (e) => {
      if (e.key === '/' && !e.target.matches('input, textarea')) {
        e.preventDefault();
        const el = document.getElementById('globalSearch');
        if (el) el.focus();
      }
    });
  </script>

  @stack('scripts')
</body>
</html>
