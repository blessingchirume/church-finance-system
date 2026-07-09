<?php

namespace Database\Seeders;

use App\Models\Assembly;
use App\Models\ChartAccount;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HandwrittenServiceRecordsSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $assembly = Assembly::firstOrCreate(
                ['code' => 'MAIN'],
                ['name' => 'Eastview Assembly', 'status' => 'active']
            );
            $userId = User::where('role', 'admin')->value('id') ?? User::query()->value('id');

            $accounts = ChartAccount::whereIn('code', [
                '3000',
                '4000',
                '4010',
                '4030',
                '4050',
                '4051',
                '4060',
                '4070',
                '5000',
                '5010',
                '5020',
            ])->get()->keyBy('code');

            $openingService = Service::firstOrCreate(
                ['service_date' => '2026-06-14'],
                ['description' => 'Opening balance brought forward before handwritten records']
            );
            $this->income($openingService, $assembly, $accounts['3000'], $userId, [
                'key' => 'handwritten-opening-balance-before-2026-06-21',
                'date' => '2026-06-14',
                'type' => 'other',
                'purpose' => 'Opening balance brought forward',
                'amount' => 706,
                'description' => 'Opening balance from handwritten bank reconciliation before 21 June 2026.',
            ]);

            $service21June = $this->service('2026-06-21', 'Handwritten income statement - Sunday 21 June 2026');
            $this->income($service21June, $assembly, $accounts['4010'], $userId, [
                'key' => 'handwritten-2026-06-21-offering',
                'date' => '2026-06-21',
                'type' => 'offering',
                'purpose' => 'Offering',
                'amount' => 62,
            ]);
            $this->income($service21June, $assembly, $accounts['4000'], $userId, [
                'key' => 'handwritten-2026-06-21-tithe',
                'date' => '2026-06-21',
                'type' => 'other',
                'purpose' => 'Tithe',
                'amount' => 10,
            ]);
            $this->income($service21June, $assembly, $accounts['4051'], $userId, [
                'key' => 'handwritten-2026-06-21-mukwasha-pledge',
                'date' => '2026-06-21',
                'type' => 'project_pledge',
                'purpose' => 'Mukwasha pledge',
                'amount' => 45,
            ]);
            $this->income($service21June, $assembly, $accounts['4060'], $userId, [
                'key' => 'handwritten-2026-06-21-funeral',
                'date' => '2026-06-21',
                'type' => 'funeral',
                'purpose' => 'Funeral',
                'amount' => 37,
            ]);
            $this->income($service21June, $assembly, $accounts['4030'], $userId, [
                'key' => 'handwritten-2026-06-21-calendar-sales',
                'date' => '2026-06-21',
                'type' => 'other',
                'purpose' => 'Calendar sales',
                'amount' => 4,
            ]);
            $this->expense($service21June, $assembly, $accounts['5010'], $userId, [
                'key' => 'handwritten-2026-06-21-wt-payment',
                'date' => '2026-06-21',
                'category' => 'worship',
                'purpose' => 'W/T payment',
                'amount' => 1,
            ]);

            $service28June = $this->service('2026-06-28', 'Handwritten income statement - Sunday 28 June 2026');
            $this->income($service28June, $assembly, $accounts['4010'], $userId, [
                'key' => 'handwritten-2026-06-28-offering',
                'date' => '2026-06-28',
                'type' => 'offering',
                'purpose' => 'Offering',
                'amount' => 80,
            ]);
            $this->income($service28June, $assembly, $accounts['4000'], $userId, [
                'key' => 'handwritten-2026-06-28-tithe',
                'date' => '2026-06-28',
                'type' => 'other',
                'purpose' => 'Tithe',
                'amount' => 95,
            ]);
            $this->income($service28June, $assembly, $accounts['4051'], $userId, [
                'key' => 'handwritten-2026-06-28-mukwasha-pledge',
                'date' => '2026-06-28',
                'type' => 'project_pledge',
                'purpose' => 'Mukwasha pledge',
                'amount' => 270,
            ]);
            $this->income($service28June, $assembly, $accounts['4000'], $userId, [
                'key' => 'handwritten-2026-06-28-arrears',
                'date' => '2026-06-28',
                'type' => 'other',
                'purpose' => 'Arrears',
                'amount' => 61,
                'description' => 'Handwritten amount read as 61 to reconcile income total.',
            ]);
            $this->income($service28June, $assembly, $accounts['4070'], $userId, [
                'key' => 'handwritten-2026-06-28-youth-birthday',
                'date' => '2026-06-28',
                'type' => 'other',
                'purpose' => 'Youth birthday contribution',
                'amount' => 51,
            ]);
            $this->income($service28June, $assembly, $accounts['4000'], $userId, [
                'key' => 'handwritten-2026-06-28-aloe',
                'date' => '2026-06-28',
                'type' => 'other',
                'purpose' => 'Aloe',
                'amount' => 5,
            ]);
            $this->income($service28June, $assembly, $accounts['4010'], $userId, [
                'key' => 'handwritten-2026-06-28-love-offering',
                'date' => '2026-06-28',
                'type' => 'offering',
                'purpose' => 'Love offering',
                'amount' => 29,
            ]);
            $this->income($service28June, $assembly, $accounts['4051'], $userId, [
                'key' => 'handwritten-2026-06-28-masicha-pledge',
                'date' => '2026-06-28',
                'type' => 'project_pledge',
                'purpose' => 'Masicha pledge',
                'amount' => 200,
            ]);
            foreach ([
                ['apostle-tithe', 'Payout to Apostle - tithe', 8],
                ['apostle-other', 'Payout to Apostle', 95],
                ['apostle-love-offering', 'Payout to Apostle - love offering', 29],
                ['apostle-tithe-2026-06-21', 'Payout to Apostle - tithe at 21.06.26', 10],
                ['cleaner', 'Cleaner payment', 5],
            ] as [$key, $purpose, $amount]) {
                $this->expense($service28June, $assembly, $accounts[$key === 'cleaner' ? '5000' : '5010'], $userId, [
                    'key' => "handwritten-2026-06-28-{$key}",
                    'date' => '2026-06-28',
                    'category' => $key === 'cleaner' ? 'administration' : 'worship',
                    'purpose' => $purpose,
                    'amount' => $amount,
                ]);
            }

            $service5July = $this->service('2026-07-05', 'Handwritten income statement - Sunday 05 July 2026');
            foreach ([
                ['offering', '4010', 'offering', 'Offering', 149],
                ['mukwasha-pledge', '4051', 'project_pledge', 'Mukwasha pledges', 648],
                ['tithe', '4000', 'other', 'Tithe', 150],
                ['funeral', '4060', 'funeral', 'Funeral', 66],
                ['birthday-contribution', '4070', 'other', 'Birthday contribution', 164],
                ['love-offering', '4010', 'offering', 'Love offering', 9],
                ['calendar', '4030', 'other', 'Calendar', 3],
                ['dollar-sunday', '4000', 'other', 'Dollar Sunday', 91],
                ['ecocash-deposit', '4000', 'other', 'Deposit from EcoCash', 88],
            ] as [$key, $accountCode, $type, $purpose, $amount]) {
                $this->income($service5July, $assembly, $accounts[$accountCode], $userId, [
                    'key' => "handwritten-2026-07-05-{$key}",
                    'date' => '2026-07-05',
                    'type' => $type,
                    'purpose' => $purpose,
                    'amount' => $amount,
                ]);
            }
            foreach ([
                ['hosting', '5000', 'administration', 'Church finance system hosting', 8],
                ['wendy-salary', '5000', 'administration', 'Wendy salary', 50],
                ['tongai-fuel', '5020', 'maintenance', 'Tongai fuel reimbursement', 9],
                ['apostle-tithes', '5010', 'worship', 'Apostle tithes from net collection', 159],
                ['generator-repairs', '5020', 'maintenance', 'Generator repairs', 31],
            ] as [$key, $accountCode, $category, $purpose, $amount]) {
                $this->expense($service5July, $assembly, $accounts[$accountCode], $userId, [
                    'key' => "handwritten-2026-07-05-{$key}",
                    'date' => '2026-07-05',
                    'category' => $category,
                    'purpose' => $purpose,
                    'amount' => $amount,
                ]);
            }

            Service::recomputeBalances();
        });
    }

    private function service(string $date, string $description): Service
    {
        $service = Service::firstOrCreate(['service_date' => $date], ['description' => $description]);

        if (! $service->description) {
            $service->forceFill(['description' => $description])->saveQuietly();
        }

        return $service;
    }

    private function income(Service $service, Assembly $assembly, ChartAccount $account, ?int $userId, array $data): void
    {
        Income::updateOrCreate(
            ['mobile_client_id' => $data['key']],
            [
                'assembly_id' => $assembly->id,
                'service_id' => $service->id,
                'chart_account_id' => $account->id,
                'transaction_date' => $data['date'],
                'type' => $data['type'],
                'purpose' => $data['purpose'],
                'pledge_campaign' => $data['type'] === 'project_pledge' ? $data['purpose'] : null,
                'amount' => $data['amount'],
                'currency' => 'USD',
                'payment_method' => 'cash',
                'description' => $data['description'] ?? $data['purpose'],
                'source' => 'manual',
                'status' => 'approved',
                'created_by' => $userId,
                'approved_by' => $userId,
                'approved_at' => now(),
                'submitted_from_mobile' => false,
            ]
        );
    }

    private function expense(Service $service, Assembly $assembly, ChartAccount $account, ?int $userId, array $data): void
    {
        Expense::updateOrCreate(
            ['mobile_client_id' => $data['key']],
            [
                'assembly_id' => $assembly->id,
                'service_id' => $service->id,
                'chart_account_id' => $account->id,
                'transaction_date' => $data['date'],
                'amount' => $data['amount'],
                'currency' => 'USD',
                'payment_method' => 'cash',
                'description' => $data['purpose'],
                'category' => $data['category'],
                'purpose' => $data['purpose'],
                'status' => 'approved',
                'created_by' => $userId,
                'approved_by' => $userId,
                'approved_at' => now(),
                'submitted_from_mobile' => false,
            ]
        );
    }
}
