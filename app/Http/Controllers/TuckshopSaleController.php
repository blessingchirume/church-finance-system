<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\TuckshopSale;
use Illuminate\Http\Request;

class TuckshopSaleController extends Controller
{
    public function index()
    {
        $sales = TuckshopSale::with('service')
            ->latest()
            ->paginate(15);

        return view('tuckshop-sales.index', compact('sales'));
    }

    public function create()
    {
        $services = Service::orderBy('service_date', 'desc')->get();
        return view('tuckshop-sales.create', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'external_reference' => 'required|string|max:255|unique:tuckshop_sales',
            'service_id' => 'nullable|exists:services,id',
            'amount' => 'required|numeric|min:0',
        ]);

        TuckshopSale::create($validated);

        return redirect()->route('tuckshop-sales.index')
            ->with('success', 'Tuckshop sale recorded successfully');
    }

    public function edit(TuckshopSale $tuckshopSale)
    {
        $services = Service::orderBy('service_date', 'desc')->get();
        return view('tuckshop-sales.edit', compact('tuckshopSale', 'services'));
    }

    public function update(Request $request, TuckshopSale $tuckshopSale)
    {
        $validated = $request->validate([
            'external_reference' => 'required|string|max:255|unique:tuckshop_sales,external_reference,'.$tuckshopSale->id,
            'service_id' => 'nullable|exists:services,id',
            'amount' => 'required|numeric|min:0',
        ]);

        $tuckshopSale->update($validated);

        return redirect()->route('tuckshop-sales.index')
            ->with('success', 'Tuckshop sale updated successfully');
    }

    public function destroy(TuckshopSale $tuckshopSale)
    {
        $tuckshopSale->delete();
        return redirect()->route('tuckshop-sales.index')
            ->with('success', 'Tuckshop sale deleted successfully');
    }
}
