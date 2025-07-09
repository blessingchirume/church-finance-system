<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
class ReportController extends Controller
{

    public function generateServiceReport($id)
    {
        $service = Service::with(['incomes', 'expenses'])->findOrFail($id);
        $pdf = Pdf::loadView('reports.service', compact('service'));
        return $pdf->download("Service_Report_{$service->service_date}.pdf");
    }
}
