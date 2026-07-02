@extends('layouts.app')

@section('page-title', isset($income) ? 'Edit Receipt' : 'Record Receipt')

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="mb-6">
            <h1 class="text-2xl font-semibold tracking-tight text-slate-950">{{ isset($income) ? 'Edit Income Record' : 'Record Income' }}</h1>
            <p class="mt-1 text-sm text-slate-600">Assign each receipt to the correct G/L account for fund and category reporting.</p>
        </div>

        <form method="POST" action="{{ isset($income) ? route('incomes.update', $income) : route('incomes.store') }}" class="rounded-md border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @isset($income)
                @method('PUT')
            @endisset

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="chart_account_id" class="block text-sm font-semibold text-slate-700">G/L Account</label>
                    <select id="chart_account_id" name="chart_account_id" required class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                        <option value="">Select income account</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" @selected((int) old('chart_account_id', $income->chart_account_id ?? null) === $account->id)>{{ $account->display_name }}</option>
                        @endforeach
                    </select>
                    @error('chart_account_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="type" class="block text-sm font-semibold text-slate-700">Receipt Type</label>
                    <select id="type" name="type" required class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                        <option value="">Select type</option>
                        @foreach($types as $type)
                            <option value="{{ $type }}" @selected(old('type', $income->type ?? null) === $type)>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                        @endforeach
                    </select>
                    @error('type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="amount" class="block text-sm font-semibold text-slate-700">Amount</label>
                    <input type="number" step="0.01" id="amount" name="amount" min="0" value="{{ old('amount', $income->amount ?? null) }}" required class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                    @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-semibold text-slate-700">Status</label>
                    <select id="status" name="status" required class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" @selected(old('status', $income->status ?? 'approved') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                        @endforeach
                    </select>
                    @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="member_id" class="block text-sm font-semibold text-slate-700">Member</label>
                    <select id="member_id" name="member_id" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                        <option value="">No member selected</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" @selected((int) old('member_id', $income->member_id ?? null) === $member->id)>{{ $member->name }}</option>
                        @endforeach
                    </select>
                    @error('member_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="pledge_campaign" class="block text-sm font-semibold text-slate-700">Pledge Campaign</label>
                    <input id="pledge_campaign" name="pledge_campaign" value="{{ old('pledge_campaign', $income->pledge_campaign ?? null) }}" placeholder="Building Fund, Solar Pledges, Missions" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                    @error('pledge_campaign') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="service_id" class="block text-sm font-semibold text-slate-700">Service</label>
                    <select id="service_id" name="service_id" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                        <option value="">No service selected</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" @selected((int) old('service_id', $income->service_id ?? null) === $service->id)>{{ $service->service_date }}</option>
                        @endforeach
                    </select>
                    @error('service_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="project_id" class="block text-sm font-semibold text-slate-700">Project / Fund</label>
                    <select id="project_id" name="project_id" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                        <option value="">No project selected</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" @selected((int) old('project_id', $income->project_id ?? null) === $project->id)>{{ $project->name }}</option>
                        @endforeach
                    </select>
                    @error('project_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="source" class="block text-sm font-semibold text-slate-700">Source</label>
                    <select id="source" name="source" required class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                        @foreach($sources as $source)
                            <option value="{{ $source }}" @selected(old('source', $income->source ?? 'manual') === $source)>{{ ucfirst($source) }}</option>
                        @endforeach
                    </select>
                    @error('source') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-semibold text-slate-700">Description / Memo</label>
                    <textarea id="description" name="description" rows="4" class="mt-1 w-full rounded-md border-slate-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">{{ old('description', $income->description ?? null) }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('incomes.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Save Income</button>
            </div>
        </form>
    </div>
@endsection
