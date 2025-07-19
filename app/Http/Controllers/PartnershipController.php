<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartnershipRequest;
use App\Http\Requests\RecordPaymentRequest;
use App\Models\Member;
use App\Models\Partner;
use App\Models\User;
use App\Services\PartnershipService;
use Illuminate\Http\Request;

class PartnershipController extends Controller
{
    protected $partnershipService;

    public function __construct(PartnershipService $partnershipService)
    {
        $this->partnershipService = $partnershipService;
    }

    public function index()
    {
         $members = Member::whereNotIn('id', Partner::pluck('member_id'))->get();
        $partners = Partner::with(['user', 'arrears' => function($query) {
            $query->where('is_settled', false);
        }])->paginate(20);

        return view('partnerships.index', compact('partners', 'members'));
    }

    public function create()
    {
        $members = Member::whereNotIn('id', Partner::pluck('member_id'))->get();
        return view('partnerships.create', compact('members'));
    }

    public function store(StorePartnershipRequest $request)
    {
        $partner = $this->partnershipService->registerPartnership(
            $request->member_id,
            $request->amount
        );

        return redirect()->route('partnerships.show', $partner->id)
            ->with('success', 'Partnership registered successfully');
    }

    public function show($id)
    {
        $history = $this->partnershipService->getPartnerPaymentHistory($id);
        return view('partnerships.show', compact('history'));
    }

    public function recordPaymentForm($id)
    {
        $partner = Partner::with('user')->findOrFail($id);
        return view('partnerships.record-payment', compact('partner'));
    }

    public function recordPayment(RecordPaymentRequest $request, $id)
    {
        $payment = $this->partnershipService->recordPayment(
            $id,
            $request->amount,
            $request->payment_method,
            $request->reference
        );

        return redirect()->route('partnerships.show', $id)
            ->with('success', 'Payment recorded successfully');
    }

    public function reports(Request $request)
    {
        // dd('This is the reports page. Implement your report logic here.');
        $month = $request->month ?? now()->format('Y-m');
        $report = $this->partnershipService->getMonthlyArrearsReport($month . '-01');

        $expectedIncome = $this->partnershipService->getExpectedMonthlyIncome();
        $actualIncome = $this->partnershipService->getActualMonthlyIncome($month . '-01');

        return view('partnerships.reports', compact(
            'report',
            'expectedIncome',
            'actualIncome',
            'month'
        ));
    }
}