<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('service_date', 'desc')
            ->paginate(15);

        return view('services.index', compact('services'));
    }

    public function create()
    {
        return view('services.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_date' => 'required|date',
            'description' => 'nullable|string',
            'opening_balance' => 'required|numeric|min:0',
            'closing_balance' => 'required|numeric|min:0',
        ]);

        Service::create($validated);

        return redirect()->route('services.index')
            ->with('success', 'Service recorded successfully');
    }

    public function show(Service $service)
    {
        return view('services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        return view('services.form', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'service_date' => 'required|date',
            'description' => 'nullable|string',
            'opening_balance' => 'required|numeric|min:0',
            'closing_balance' => 'required|numeric|min:0',
        ]);

        $service->update($validated);

        return redirect()->route('services.index')
            ->with('success', 'Service updated successfully');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('services.index')
            ->with('success', 'Service deleted successfully');
    }
}
