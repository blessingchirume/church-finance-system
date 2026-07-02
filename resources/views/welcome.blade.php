<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Foundation of Hope Finance Portal</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 font-sans text-slate-900 antialiased">
    <div class="min-h-screen">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('logo.jpg') }}" alt="Foundation of Hope" class="h-11 w-11 rounded-md object-cover">
                    <div>
                        <div class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Foundation of Hope</div>
                        <div class="text-xs font-medium text-slate-500">Church Finance Administration</div>
                    </div>
                </div>

                <nav class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Open Portal</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">Staff Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="hidden rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:inline-flex">Register</a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            <section class="mx-auto grid max-w-7xl gap-10 px-4 py-12 sm:px-6 lg:grid-cols-[1.05fr_0.95fr] lg:px-8 lg:py-16">
                <div class="flex flex-col justify-center">
                    <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Restricted finance workspace</p>
                    <h1 class="mt-4 max-w-3xl text-4xl font-semibold tracking-tight text-slate-950 sm:text-5xl">
                        Stewardship, reporting, and approvals for church funds.
                    </h1>
                    <p class="mt-5 max-w-2xl text-base leading-7 text-slate-600">
                        Manage offerings, pledges, funeral contributions, expenses, fund balances, and audit-ready reports from one professional finance portal.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-md bg-emerald-600 px-5 py-3 text-center text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Go to Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-md bg-emerald-600 px-5 py-3 text-center text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Login to Finance Portal</a>
                        @endauth
                        <a href="#modules" class="rounded-md border border-slate-300 px-5 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-white">View Modules</a>
                    </div>
                </div>

                <div class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                        <div>
                            <h2 class="text-base font-semibold text-slate-950">Finance Control Center</h2>
                            <p class="mt-1 text-sm text-slate-500">Snapshot of portal responsibilities</p>
                        </div>
                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">Admin</span>
                    </div>

                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-md border border-slate-200 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Income</p>
                            <p class="mt-2 text-lg font-semibold text-slate-950">Offerings & Pledges</p>
                        </div>
                        <div class="rounded-md border border-slate-200 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Payments</p>
                            <p class="mt-2 text-lg font-semibold text-slate-950">Expenses & Funds</p>
                        </div>
                        <div class="rounded-md border border-slate-200 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Accounts</p>
                            <p class="mt-2 text-lg font-semibold text-slate-950">Chart of Accounts</p>
                        </div>
                        <div class="rounded-md border border-slate-200 p-4">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Oversight</p>
                            <p class="mt-2 text-lg font-semibold text-slate-950">Roles & Audit Trail</p>
                        </div>
                    </div>
                </div>
            </section>

            <section id="modules" class="border-y border-slate-200 bg-white">
                <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
                    <div class="grid gap-4 md:grid-cols-3">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-950">Built for accountable ministry finance</h2>
                            <p class="mt-2 text-sm leading-6 text-slate-600">The portal keeps receipts, approvals, account balances, and reports tied to a structured G/L.</p>
                        </div>
                        <div class="rounded-md border border-slate-200 p-5">
                            <h3 class="font-semibold text-slate-950">Fund Tracking</h3>
                            <p class="mt-2 text-sm text-slate-600">Track funeral funds, building funds, missions, welfare, solar pledges, and general revenue separately.</p>
                        </div>
                        <div class="rounded-md border border-slate-200 p-5">
                            <h3 class="font-semibold text-slate-950">Reports</h3>
                            <p class="mt-2 text-sm text-slate-600">Review account balances, income by category, expenses by category, pledge collections, and date-range transactions.</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
