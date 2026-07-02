<?php 

namespace App\Services;

use App\Models\Partner;
use App\Models\PartnerArrear;
use App\Models\PartnerPayment;
use Carbon\Carbon;

class PartnershipService
{
    public function registerPartnership($userId, $amount)
    {
        return Partner::updateOrCreate(
            ['member_id' => $userId],
            [
                'commitment_amount' => $amount,
                'commitment_start_date' => now(),
                'is_active' => true
            ]
        );
    }

    public function recordPayment($partnerId, $amount, $paymentMethod, $reference = null)
    {
        $partner = Partner::findOrFail($partnerId);
        $currentMonth = now()->startOfMonth()->format('Y-m-d');

        $payment = PartnerPayment::create([
            'partner_id' => $partner->id,
            'amount_paid' => $amount,
            'payment_date' => now(),
            'payment_method' => $paymentMethod,
            'reference_number' => $reference,
            'month_year' => $currentMonth
        ]);

        $this->updateArrears($partner, $currentMonth, $amount);

        return $payment;
    }

    private function updateArrears(Partner $partner, $monthYear, $amountPaid)
    {
        $arrear = PartnerArrear::firstOrNew([
            'partner_id' => $partner->id,
            'month_year' => $monthYear
        ]);

        $arrear->expected_amount = $partner->commitment_amount;
        $arrear->amount_paid = ($arrear->amount_paid ?? 0) + $amountPaid;
        $arrear->balance = $arrear->expected_amount - $arrear->amount_paid;
        $arrear->is_settled = $arrear->balance <= 0;
        $arrear->save();
    }

    public function calculateMonthlyArrears()
    {
        $currentMonth = now()->startOfMonth()->format('Y-m-d');
        $previousMonth = now()->subMonth()->startOfMonth()->format('Y-m-d');

        $activePartners = Partner::where('is_active', true)->get();

        foreach ($activePartners as $partner) {
            $paymentExists = PartnerPayment::where('partner_id', $partner->id)
                ->where('month_year', $currentMonth)
                ->exists();

            if (!$paymentExists) {
                PartnerArrear::updateOrCreate(
                    [
                        'partner_id' => $partner->id,
                        'month_year' => $currentMonth
                    ],
                    [
                        'expected_amount' => $partner->commitment_amount,
                        'amount_paid' => 0,
                        'balance' => $partner->commitment_amount,
                        'is_settled' => false
                    ]
                );
            }

            $previousArrear = PartnerArrear::where('partner_id', $partner->id)
                ->where('month_year', $previousMonth)
                ->where('is_settled', false)
                ->first();

            if ($previousArrear) {
                $this->recordArrearCarryOver($partner, $previousArrear, $currentMonth);
            }
        }
    }

    private function recordArrearCarryOver(Partner $partner, PartnerArrear $previousArrear, $currentMonth)
    {
        $arrear = PartnerArrear::firstOrNew([
            'partner_id' => $partner->id,
            'month_year' => $currentMonth
        ]);

        $arrear->expected_amount = $partner->commitment_amount + $previousArrear->balance;
        $arrear->amount_paid = $arrear->amount_paid ?? 0;
        $arrear->balance = $arrear->expected_amount - $arrear->amount_paid;
        $arrear->is_settled = $arrear->balance <= 0;
        $arrear->save();
    }

    public function getExpectedMonthlyIncome()
    {
        return Partner::where('is_active', true)->sum('commitment_amount');
    }

    public function getActualMonthlyIncome($month = null)
    {
        $month = $month ?? now()->startOfMonth()->format('Y-m-d');
        return PartnerPayment::where('month_year', $month)->sum('amount_paid');
    }

    public function getMonthlyArrearsReport($month = null)
    {
        $month = $month ?? now()->startOfMonth()->format('Y-m-d');

        $report = [
            'total_expected' => PartnerArrear::where('month_year', $month)->sum('expected_amount'),
            'total_received' => PartnerArrear::where('month_year', $month)->sum('amount_paid'),
            'total_arrears' => PartnerArrear::where('month_year', $month)->sum('balance'),
            'partners_in_arrears' => PartnerArrear::with('partner.user')
                ->where('month_year', $month)
                ->where('balance', '>', 0)
                ->get()
                ->map(function ($arrear) {
                    return [
                        'id' => $arrear->partner->user->id,
                        'name' => $arrear->partner->user->name,
                        'email' => $arrear->partner->user->email,
                        'expected' => $arrear->expected_amount,
                        'paid' => $arrear->amount_paid,
                        'balance' => $arrear->balance
                    ];
                })
        ];

        return $report;
    }

    public function getPartnerPaymentHistory($partnerId)
    {
        $partner = Partner::with(['user', 'payments', 'arrears' => function($query) {
            $query->where('balance', '>', 0)->orderBy('month_year');
        }])->findOrFail($partnerId);

        return [
            'partner' => [
                'id' => $partner->user->id,
                'name' => $partner->user->name,
                'email' => $partner->user->email,
                'commitment' => $partner->commitment_amount,
                'start_date' => $partner->commitment_start_date->format('Y-m-d'),
                'is_active' => $partner->is_active
            ],
            'payments' => $partner->payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => $payment->amount_paid,
                    'date' => $payment->payment_date->format('Y-m-d'),
                    'method' => $payment->payment_method,
                    'reference' => $payment->reference_number,
                    'month' => $payment->month_year->format('Y-m')
                ];
            }),
            'arrears' => $partner->arrears->map(function ($arrear) {
                return [
                    'month' => $arrear->month_year->format('Y-m'),
                    'expected' => $arrear->expected_amount,
                    'paid' => $arrear->amount_paid,
                    'balance' => $arrear->balance
                ];
            }),
            'total_arrears' => $partner->arrears->sum('balance')
        ];
    }
}