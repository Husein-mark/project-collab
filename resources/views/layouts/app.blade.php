<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Pengelolaan Studio') - Studio Sekolah</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">
    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left: Brand & Navigation -->
                <div class="flex items-center space-x-8">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-700 to-indigo-500 flex items-center justify-center text-white shadow-md shadow-brand-500/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <span class="text-base font-bold text-slate-900 tracking-tight block">StudioHub</span>
                            <span class="text-xs text-slate-500 block">Sistem Peminjaman Studio</span>
                        </div>
                    </a>

                    @auth
                    <nav class="hidden md:flex space-x-1">
                        <a href="{{ route('dashboard') }}" 
                           class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Dashboard
                        </a>

                        <a href="{{ route('items.index') }}" 
                           class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('items.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Daftar Barang
                        </a>

                        @if(auth()->user()->isSiswa())
                        <a href="{{ route('loans.create') }}" 
                           class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('loans.create') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Ajukan Pinjam
                        </a>
                        <a href="{{ route('loans.my') }}" 
                           class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('loans.my') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Peminjaman Saya
                        </a>
                        @endif

                        @if(auth()->user()->isGuru())
                        <a href="{{ route('loans.index') }}" 
                           class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('loans.index') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Approval & Peminjaman
                        </a>
                        <a href="{{ route('notices.index') }}" 
                           class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('notices.*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Notice Kerusakan
                        </a>
                        <a href="{{ route('reports.index') }}" 
                           class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('reports.index') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Laporan & Monitoring
                        </a>
                        <a href="{{ route('reports.students') }}" 
                           class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('reports.students*') ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Kedisiplinan Siswa
                        </a>
                        @endif
                    </nav>
                    @endauth
                </div>

                <!-- Right: Profile & Actions -->
                <div class="flex items-center space-x-3">
                    @auth
                    <div class="flex items-center space-x-3 pl-4 border-l border-slate-200">
                        <div class="text-right">
                            <div class="text-sm font-semibold text-slate-800 leading-none">{{ auth()->user()->name }}</div>
                            <span class="inline-block px-2 py-0.5 mt-1 text-[11px] font-semibold rounded-full {{ auth()->user()->isGuru() ? 'bg-indigo-100 text-indigo-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ auth()->user()->isGuru() ? 'Guru / Pengelola' : 'Siswa' }}
                            </span>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    title="Keluar" 
                                    class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        Masuk
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    <main class="flex-1 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg shadow-xs flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="h-5 w-5 text-emerald-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-lg shadow-xs flex items-center">
                <svg class="h-5 w-5 text-rose-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm font-medium text-rose-800">{{ session('error') }}</p>
            </div>
            @endif

            @if(session('warning'))
            <div class="mb-6 bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-lg shadow-xs flex items-center">
                <svg class="h-5 w-5 text-amber-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <p class="text-sm font-medium text-amber-800">{{ session('warning') }}</p>
            </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} Pengelolaan Studio Sekolah. Dikembangkan berdasarkan Studi Kasus LKPD Pengelolaan Studio.</p>
        </div>
    </footer>
</body>
</html>
