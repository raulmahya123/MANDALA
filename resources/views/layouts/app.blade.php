<!doctype html>
<html lang="id" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? 'Mandala Portal — Browse' }}</title>

  {{-- Tailwind + Vite --}}
  @vite(['resources/css/app.css','resources/js/app.js'])

  <style>
    .hero-bg{background-size:cover;background-position:center}
  </style>
</head>
<body class="h-full bg-slate-50 text-slate-800">

  {{-- ======= HEADER ======= --}}
  <header class="bg-white border-b sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-4">
      {{-- Brand --}}
      <a href="{{ route('home') }}" class="font-semibold tracking-wide flex items-center gap-2">
        <span class="inline-flex h-7 w-7 rounded-xl bg-gradient-to-br from-emerald-500 via-lime-400 to-cyan-400"></span>
        <span class="hidden sm:inline">MANDALA</span>
      </a>

      {{-- Nav utama --}}
      <nav class="hidden md:flex items-center gap-4 text-sm">
        <a href="{{ route('home') }}" class="text-emerald-600 font-medium">Browse</a>
        @if(Route::has('forms.index'))
        <a href="{{ route('forms.index') }}" class="hover:text-emerald-600">Forms</a>
        @endif
        @auth
          @if(Route::has('user.entries.index'))
          <a href="{{ route('user.entries.index') }}" class="hover:text-emerald-600">Entri Saya</a>
          @endif
          @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'admin')
          <a href="{{ route('admin.documents.index') }}" class="hover:text-emerald-600">Admin</a>
          @endif
        @endauth
      </nav>

      {{-- Auth --}}
      <div class="flex items-center gap-3">
        @auth
          <details class="relative">
            <summary class="list-none cursor-pointer flex items-center gap-2 px-2 py-1.5 rounded-xl border bg-white hover:bg-slate-50">
              <img src="https://api.dicebear.com/8.x/identicon/svg?seed={{ urlencode(auth()->user()->email) }}" class="h-7 w-7 rounded-lg" alt="avatar">
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
          @if (Route::has('register'))
          <a href="{{ route('register') }}" class="hidden sm:inline px-3 py-1 rounded-xl ring-1 ring-inset ring-gray-300 bg-white hover:bg-gray-50">Register</a>
          @endif
          <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white hover:bg-emerald-700">Login</a>
        @endauth
      </div>
    </div>

    {{-- Mobile subnav --}}
    <div class="md:hidden border-t">
      <div class="max-w-7xl mx-auto px-4 py-2 flex items-center gap-4 text-sm">
        <a href="{{ route('home') }}" class="text-emerald-600 font-medium">Browse</a>
        @if(Route::has('forms.index'))
        <a href="{{ route('forms.index') }}" class="hover:text-emerald-600">Forms</a>
        @endif
        @auth
          @if(Route::has('user.entries.index'))
          <a href="{{ route('user.entries.index') }}" class="hover:text-emerald-600">Entri Saya</a>
          @endif
          @if(auth()->user()->role === 'super_admin' || auth()->user()->role === 'admin')
          <a href="{{ route('admin.documents.index') }}" class="hover:text-emerald-600">Admin</a>
          @endif
        @endauth
      </div>
    </div>
  </header>

  {{-- ======= MAIN ======= --}}
  <main class="max-w-7xl mx-auto px-4 py-6">

    {{-- Alert flash standar (opsional) --}}
    @if(session('status') || session('ok') || session('error'))
      <div class="mb-4">
        @if(session('ok') || session('status'))
          <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3">{{ session('ok') ?? session('status') }}</div>
        @endif
        @if(session('error'))
          <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 mt-2">{{ session('error') }}</div>
        @endif
      </div>
    @endif

    {{-- ===== Hero ===== --}}
    <section class="relative overflow-hidden rounded-3xl border bg-gradient-to-b from-slate-900 to-slate-800 text-white">
      <div class="absolute inset-0 opacity-20 hero-bg"
           style="background-image:url('https://images.unsplash.com/photo-1606313564200-e75d5e30476e?q=80&w=1400&auto=format&fit=crop');"></div>
      <div class="relative px-6 py-12 lg:px-10">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
          <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-200 border border-emerald-400/30 text-xs mb-3">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.8" stroke-linecap="round" d="M3.75 9 12 3.75 20.25 9M6 10.5v9.75h12V10.5"/></svg>
              Portal Operasional Tambang
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight leading-tight">
              Dokumen Operasional & Form Pengajuan<br class="hidden sm:block">
              <span class="text-emerald-400">terpusat & bisa dicari</span>
            </h1>
            <p class="mt-3 text-slate-200/80">
              Akses SOP, IK, Form, dan dokumen teknis dari setiap departemen. Rampingkan alur kerja—dari pit hingga head office.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
              <a href="#departments" class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2.5">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.8" stroke-linecap="round" d="m6 9 6 6 6-6"/></svg>
                Jelajahi Departemen
              </a>
              @if(Route::has('forms.index'))
              <a href="{{ route('forms.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-white/10 hover:bg-white/15 text-white px-4 py-2.5 border border-white/20">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.8" stroke-linecap="round" d="M8 7h8M8 12h8M8 17h5M6 3.75h12A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6A2.25 2.25 0 0 1 6 3.75Z"/></svg>
                Lihat Form
              </a>
              @endif
            </div>
          </div>

          {{-- KPI mini --}}
          <div class="grid grid-cols-3 divide-x divide-white/10 rounded-2xl border border-white/10 bg-white/5 backdrop-blur p-4 min-w-[260px]">
            <div class="px-3">
              <div class="text-xs uppercase tracking-wide text-white/70">Departemen</div>
              <div class="text-2xl font-bold">{{ ($departments ?? collect())->count() }}</div>
            </div>
            <div class="px-3">
              <div class="text-xs uppercase tracking-wide text-white/70">Kategori</div>
              <div class="text-2xl font-bold">{{ $categoriesTotal ?? 0 }}</div>
            </div>
            <div class="px-3">
              <div class="text-xs uppercase tracking-wide text-white/70">Dokumen</div>
              <div class="text-2xl font-bold">{{ $documentsTotal ?? 0 }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- ===== Cari & Filter ===== --}}
    <section class="mt-8">
      <div class="rounded-2xl border bg-white p-4 sm:p-5">
        <form method="GET" action="{{ route('home') }}" class="grid md:grid-cols-5 gap-3">
          <div class="md:col-span-3">
            <label class="text-xs font-medium text-slate-500">Cari</label>
            <div class="mt-1 relative">
              <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari departemen, SOP, IK, Form…"
                     class="w-full rounded-xl border-slate-200 px-4 py-2.5 pl-10">
              <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="11" cy="11" r="7" stroke-width="1.8"/><path stroke-width="1.8" d="m20 20-3.5-3.5"/>
              </svg>
            </div>
          </div>
          <div>
            <label class="text-xs font-medium text-slate-500">Region</label>
            <select name="region" class="w-full mt-1 rounded-xl border-slate-200 px-3 py-2.5">
              <option value="">Semua</option>
              @foreach(($regions ?? []) as $r)
                <option value="{{ $r }}" @selected(request('region')===$r)>{{ $r }}</option>
              @endforeach
            </select>
          </div>
          <div class="flex items-end">
            <button class="w-full rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5">Terapkan</button>
          </div>
        </form>
      </div>
    </section>

    {{-- ===== Grid Departemen ===== --}}
    <section id="departments" class="mt-8">
      <div class="mb-3 flex items-center justify-between">
        <h2 class="text-lg font-semibold">Departemen</h2>
        <div class="text-sm text-slate-500">{{ ($departments ?? collect())->count() }} unit</div>
      </div>

      @if(($departments ?? collect())->isEmpty())
        <div class="rounded-2xl border bg-white p-6 text-center text-slate-500">
          Belum ada departemen aktif.
        </div>
      @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
          @foreach($departments as $d)
          <a href="{{ route('browse.department', $d) }}"
             class="group relative overflow-hidden rounded-2xl border bg-white hover:shadow-lg transition">
            {{-- Banner --}}
            <div class="h-24 bg-gradient-to-br from-amber-700 via-amber-600 to-emerald-600 relative">
              <div class="absolute inset-0 opacity-20 hero-bg"
                   style="background-image:url('https://images.unsplash.com/photo-1581090698573-8a4bfa400d0f?q=80&w=1200&auto=format&fit=crop');"></div>
              <div class="absolute right-3 bottom-3 inline-flex items-center gap-1.5 rounded-lg bg-black/30 text-white px-2 py-1 text-xs">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.6" d="M3 5h18M3 12h18M3 19h12"/></svg>
                {{ $d->active_types_count ?? ($d->doc_types_count ?? 0) }} kategori
              </div>
            </div>

            {{-- Body --}}
            <div class="p-4">
              <div class="flex items-center gap-2">
                <span class="inline-flex h-8 w-8 rounded-lg bg-emerald-100 text-emerald-700 items-center justify-center">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-width="1.6" stroke-linecap="round" d="M3.75 21h16.5M4.5 21V5.25A2.25 2.25 0 0 1 6.75 3h4.5A2.25 2.25 0 0 1 13.5 5.25V21m-9-12h9m-9 4.5h9"/>
                  </svg>
                </span>
                <h3 class="font-semibold text-slate-800 group-hover:text-emerald-700 transition">
                  {{ $d->name }}
                </h3>
              </div>

              <div class="mt-2 text-sm text-slate-500 line-clamp-2">
                {{ $d->summary ?? 'Dokumen & form departemen terkait operasi tambang.' }}
              </div>

              <div class="mt-3 flex flex-wrap gap-2">
                @foreach(($d->tags ?? []) as $t)
                  <span class="text-xs px-2 py-1 rounded-lg border bg-slate-50 text-slate-600">{{ $t }}</span>
                @endforeach
                @if(empty($d->tags))
                  <span class="text-xs px-2 py-1 rounded-lg border bg-slate-50 text-slate-600">SOP</span>
                  <span class="text-xs px-2 py-1 rounded-lg border bg-slate-50 text-slate-600">IK</span>
                  <span class="text-xs px-2 py-1 rounded-lg border bg-slate-50 text-slate-600">Form</span>
                @endif
              </div>
            </div>

            {{-- Footer --}}
            <div class="px-4 pb-4">
              <div class="flex items-center justify-between text-xs text-slate-500">
                <div class="inline-flex items-center gap-1">
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-width="1.6" d="M12 21a9 9 0 1 0-9-9 9 9 0 0 0 9 9Z"/>
                    <path stroke-width="1.6" d="M12 7v5l3 2"/>
                  </svg>
                  Update: {{ optional($d->updated_at)->diffForHumans() ?? '–' }}
                </div>
                <div class="inline-flex items-center gap-1 text-emerald-700">
                  Lihat
                  <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.8" stroke-linecap="round" d="m9 5 7 7-7 7"/></svg>
                </div>
              </div>
            </div>
          </a>
          @endforeach
        </div>
      @endif
    </section>

    {{-- ===== CTA ===== --}}
    @if(Route::has('forms.index'))
    <section class="mt-10">
      <div class="rounded-2xl border bg-white p-5 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
          <h3 class="font-semibold">Percepat proses operasional.</h3>
          <p class="text-sm text-slate-600">Ajukan pengadaan, izin kerja, atau form harian langsung dari portal.</p>
        </div>
        <a href="{{ route('forms.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5">
          Buka Form
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.8" stroke-linecap="round" d="m9 5 7 7-7 7"/></svg>
        </a>
      </div>
    </section>
    @endif

  </main>

</body>
</html>
