@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'match' => 'dashboard', 'icon' => 'M3 13h8V3H3v10Zm10 8h8V3h-8v18ZM3 21h8v-6H3v6Z'],
        ['label' => 'Assemblies', 'route' => 'assemblies.index', 'match' => 'assemblies.*', 'icon' => 'M4 20V8l8-4 8 4v12M9 20v-6h6v6'],
        ['label' => 'Chart of Accounts', 'route' => 'chart-accounts.index', 'match' => 'chart-accounts.*', 'icon' => 'M4 7h16M4 12h16M4 17h10'],
        ['label' => 'Income / Receipts', 'route' => 'incomes.index', 'match' => 'incomes.*', 'icon' => 'M12 3v18m7-11-7-7-7 7m14 4H5'],
        ['label' => 'Expenses / Payments', 'route' => 'expenses.index', 'match' => 'expenses.*', 'icon' => 'M12 21V3m7 11-7 7-7-7M5 10h14'],
        ['label' => 'Pledges', 'route' => 'partnerships.index', 'match' => 'partnerships.*', 'icon' => 'M6 7h12M6 12h12M6 17h8'],
        ['label' => 'Members', 'route' => 'members.index', 'match' => 'members.*', 'icon' => 'M16 11c1.657 0 3-1.79 3-4s-1.343-4-3-4-3 1.79-3 4 1.343 4 3 4ZM8 11c1.657 0 3-1.79 3-4S9.657 3 8 3 5 4.79 5 7s1.343 4 3 4Zm0 2c-2.667 0-5 1.333-5 3v2h10v-2c0-1.667-2.333-3-5-3Zm8 0c-.443 0-.86.037-1.25.106 1.382.758 2.25 1.797 2.25 2.894v2h4v-2c0-1.667-2.333-3-5-3Z'],
        ['label' => 'Services', 'route' => 'services.index', 'match' => 'services.*', 'icon' => 'M7 3v4M17 3v4M4 9h16M5 5h14a1 1 0 0 1 1 1v14H4V6a1 1 0 0 1 1-1Z'],
        ['label' => 'Projects / Funds', 'route' => 'projects.index', 'match' => 'projects.*', 'icon' => 'M4 6h16v12H4zM8 10h8M8 14h5'],
        ['label' => 'Reports', 'route' => 'finance-reports.index', 'match' => 'finance-reports.*', 'icon' => 'M5 19V5h14v14H5Zm3-3h2V9H8v7Zm4 0h2V7h-2v9Zm4 0h2v-5h-2v5Z'],
        ['label' => 'Users & Roles', 'route' => 'users.index', 'match' => 'users.*', 'icon' => 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 8a7 7 0 0 1 14 0'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Church Finance Portal') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 font-sans text-slate-900 antialiased">
<div x-data="{ sidebarOpen: false }" class="min-h-screen lg:flex">
    <aside class="fixed inset-y-0 left-0 z-40 w-72 transform bg-slate-950 text-white transition-transform duration-200 lg:static lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <div class="flex h-16 items-center gap-3 border-b border-white/10 px-6">
            <img src="{{ asset('logo.jpg') }}" alt="Foundation of Hope" class="h-10 w-10 rounded-md object-cover">
            <div>
                <div class="text-sm font-semibold uppercase tracking-wide text-emerald-300">Foundation of Hope</div>
                <div class="text-xs text-slate-300">Finance Admin Portal</div>
            </div>
        </div>

        <nav class="space-y-1 px-3 py-5">
            @foreach($navItems as $item)
                @continue(! Route::has($item['route']))
                @continue($item['route'] === 'users.index' && ! Auth::user()->hasRole('admin'))
                @continue($item['route'] === 'assemblies.index' && ! Auth::user()->hasRole('admin'))
                @php $active = request()->routeIs($item['match']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-medium transition {{ $active ? 'bg-emerald-500 text-slate-950' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
                    <svg class="h-5 w-5 flex-none" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="{{ $item['icon'] }}" />
                    </svg>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>
    </aside>

    <div class="fixed inset-0 z-30 bg-slate-900/50 lg:hidden" x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"></div>

    <div class="min-w-0 flex-1">
        <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur">
            <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <button type="button" class="rounded-md p-2 text-slate-600 hover:bg-slate-100 lg:hidden" @click="sidebarOpen = true">
                        <span class="sr-only">Open navigation</span>
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div>
                        <div class="text-sm font-semibold text-slate-950">@yield('page-title', 'Dashboard')</div>
                        <div class="text-xs text-slate-500">Controls, reporting, approvals, and audit-ready finance records</div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="hidden rounded-full bg-slate-100 px-3 py-1 text-xs font-medium capitalize text-slate-700 sm:inline-flex">
                        {{ str_replace('_', ' ', Auth::user()->role ?? 'viewer') }}
                    </span>
                    <a href="{{ route('profile.edit') }}" class="hidden text-sm font-medium text-slate-600 hover:text-slate-950 sm:block">{{ Auth::user()->name }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-md border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <main class="px-4 py-6 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-5 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <div class="font-semibold">Please correct the highlighted fields.</div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
