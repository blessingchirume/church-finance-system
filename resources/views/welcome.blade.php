<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Church Finance Administration | Foundation of Hope</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="min-h-screen overflow-hidden">
        <header class="border-b border-slate-200 bg-white/95 backdrop-blur">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <img src="{{ asset('logo.jpg') }}" alt="Foundation of Hope" class="h-12 w-12 rounded-lg object-cover shadow-sm ring-1 ring-slate-200">
                    <div>
                        <div class="text-sm font-bold uppercase tracking-wide text-emerald-700">Foundation of Hope</div>
                        <div class="text-xs font-medium text-slate-500">Finance Admin Portal</div>
                    </div>
                </a>

                <nav class="flex items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-lg bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Open Portal</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg bg-slate-950 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-slate-800">Staff Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="hidden rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50 sm:inline-flex">Register</a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            <section class="relative border-b border-slate-200 bg-white">
                <div class="absolute inset-0 opacity-[0.035]" style="background-image: url('{{ asset('logo.jpg') }}'); background-repeat: no-repeat; background-position: right 8% center; background-size: min(520px, 75vw);"></div>
                <div class="relative mx-auto max-w-7xl px-4 py-12 sm:px-6 sm:py-16 lg:px-8 lg:py-20">
                    <div class="max-w-3xl">
                        <div class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-emerald-700">
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                            Restricted Finance Workspace
                        </div>

                        <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-slate-950 sm:text-5xl lg:text-6xl">
                            Church Finance Administration
                        </h1>

                        <p class="mt-5 max-w-2xl text-base leading-7 text-slate-600 sm:text-lg">
                            Manage offerings, pledges, funeral contributions, expenses, reports, and approvals through a structured finance portal built for accountable church administration.
                        </p>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            @auth
                                <a href="{{ route('dashboard') }}" class="inline-flex justify-center rounded-lg bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-emerald-700">Open Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex justify-center rounded-lg bg-emerald-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-emerald-700">Staff Login</a>
                            @endauth
                            <a href="#modules" class="inline-flex justify-center rounded-lg border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50">View Modules</a>
                        </div>

                        <div class="mt-10 grid max-w-2xl grid-cols-1 gap-3 text-sm sm:grid-cols-3">
                            <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                                <div class="font-bold text-slate-950">G/L Ready</div>
                                <div class="mt-1 text-slate-500">Chart of accounts</div>
                            </div>
                            <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                                <div class="font-bold text-slate-950">Approval Flow</div>
                                <div class="mt-1 text-slate-500">Draft to approved</div>
                            </div>
                            <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                                <div class="font-bold text-slate-950">Audit Trail</div>
                                <div class="mt-1 text-slate-500">Traceable records</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="modules" class="bg-slate-50">
                <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
                    <div class="mb-7 flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight text-slate-950">Finance Modules</h2>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Core workflows for receiving, spending, classifying, approving, and reporting church funds.</p>
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 3v18M6 8l6-5 6 5M5 14h14" />
                                </svg>
                            </div>
                            <h3 class="mt-5 text-base font-bold text-slate-950">Offerings & Pledges</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Record offerings, partnerships, pledge campaigns, funeral contributions, and restricted income centers.</p>
                        </article>

                        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-red-50 text-red-700">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 21V3M6 16l6 5 6-5M5 10h14" />
                                </svg>
                            </div>
                            <h3 class="mt-5 text-base font-bold text-slate-950">Expenses & Funds</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Capture payments, withdrawals, funeral assistance, administration costs, welfare, and fund activity.</p>
                        </article>

                        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-cyan-50 text-cyan-700">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M4 7h16M4 12h16M4 17h10" />
                                </svg>
                            </div>
                            <h3 class="mt-5 text-base font-bold text-slate-950">Chart of Accounts</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Maintain account codes for assets, liabilities, income, expenses, equity, funds, and parent groupings.</p>
                        </article>

                        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                            <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 19V5h14v14H5ZM8 16h2V9H8v7ZM12 16h2V7h-2v9ZM16 16h2v-5h-2v5Z" />
                                </svg>
                            </div>
                            <h3 class="mt-5 text-base font-bold text-slate-950">Reports & Audit Trail</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Review balances, income categories, expenses, approvals, date ranges, and audit-ready transaction history.</p>
                        </article>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
