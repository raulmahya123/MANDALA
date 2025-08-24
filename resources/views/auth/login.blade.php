{{-- resources/views/auth/login.blade.php --}}
<!doctype html>
<html lang="id" class="h-full bg-gradient-to-br from-emerald-900 via-slate-900 to-black">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login — {{ config('app.name','MANDALA') }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <style>
    .grid-overlay {
      background-image:
        linear-gradient(rgba(16,185,129,.06) 1px, transparent 1px),
        linear-gradient(90deg, rgba(16,185,129,.06) 1px, transparent 1px);
      background-size: 24px 24px, 24px 24px;
      mask-image: radial-gradient(ellipse at center, rgba(0,0,0,.7), rgba(0,0,0,0) 70%);
    }
  </style>
</head>
<body class="h-full text-slate-100 relative">
  <div class="absolute inset-0 grid-overlay pointer-events-none"></div>

  <div class="min-h-full flex items-center justify-center p-6">
    <div class="w-full max-w-md">

      {{-- Brand --}}
      <div class="flex items-center justify-center mb-6 select-none">
        <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-emerald-400 via-emerald-500 to-lime-400 shadow-lg ring-1 ring-emerald-300/40 flex items-center justify-center">
          <svg class="w-7 h-7 text-slate-900" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2 3 7v10l9 5 9-5V7l-9-5Zm0 2.2 6.5 3.6v6.4L12 18.8 5.5 14.2V7.8L12 4.2Z"/>
          </svg>
        </div>
        <div class="ml-3">
          <div class="text-2xl font-extrabold tracking-tight">
            <span class="text-white">MAN</span><span class="text-emerald-400">DALA</span>
          </div>
          <div class="text-[11px] uppercase tracking-[0.2em] text-emerald-300/80">AAP PORTAL</div>
        </div>
      </div>

      {{-- Card --}}
      <div class="rounded-2xl border border-emerald-300/20 bg-slate-900/70 backdrop-blur p-6 shadow-2xl">
        @if (session('status'))
          <div class="mb-4 rounded-xl border border-emerald-300/30 bg-emerald-500/10 text-emerald-300 px-4 py-3 text-sm">
            {{ session('status') }}
          </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
          @csrf

          {{-- Email --}}
          <div>
            <label for="email" class="block text-sm mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
              class="mt-1 block w-full rounded-xl border border-slate-700 bg-slate-800/70 text-slate-100 px-3 py-2
                     focus:outline-none focus:ring-2 focus:ring-emerald-500">
            @error('email')
              <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- Password --}}
          <div>
            <label for="password" class="block text-sm mb-1">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
              class="mt-1 block w-full rounded-xl border border-slate-700 bg-slate-800/70 text-slate-100 px-3 py-2
                     focus:outline-none focus:ring-2 focus:ring-emerald-500">
            @error('password')
              <p class="text-xs text-rose-400 mt-1">{{ $message }}</p>
            @enderror
          </div>

          {{-- Remember + Forgot --}}
          <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm">
              <input id="remember_me" type="checkbox" name="remember"
                class="rounded border-slate-600 text-emerald-500 bg-slate-800 focus:ring-emerald-500">
              <span class="text-slate-300">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}"
                 class="text-sm text-emerald-300 hover:text-emerald-200 hover:underline">Lupa password?</a>
            @endif
          </div>

          {{-- Submit --}}
          <div class="pt-2 space-y-3">
            <button type="submit"
              class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                     bg-emerald-500 hover:bg-emerald-600 text-slate-900 font-semibold shadow-lg
                     focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2 focus:ring-offset-slate-900">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2Zm1 15h-2v-2h2Zm0-4h-2V7h2Z"/></svg>
              Masuk
            </button>

            @if (Route::has('register'))
              <a href="{{ route('register') }}"
                class="block w-full text-center px-4 py-2.5 rounded-xl ring-1 ring-inset ring-emerald-400/50
                       text-emerald-300 hover:bg-emerald-600/10 font-medium">
                Daftar Akun Baru
              </a>
            @endif
          </div>
        </form>
      </div>

      {{-- Footer --}}
      <p class="text-center text-[11px] text-slate-400 mt-6">
        &copy; {{ date('Y') }} {{ config('app.name','MANDALA') }} • Dibangun tangguh untuk tambang.
      </p>
    </div>
  </div>
</body>
</html>
