<?php

namespace Database\Seeders;

use App\Models\ChartAccount;
use Illuminate\Database\Seeder;

class ChartAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['1000', 'Cash and Bank', 'asset'],
            ['2000', 'Accounts Payable', 'liability'],
            ['3000', 'Equity / Church Funds', 'equity'],
            ['4000', 'General Revenue', 'income'],
            ['4010', 'Offerings', 'income'],
            ['4020', 'Partnerships', 'income'],
            ['4030', 'Calendar Sales', 'income'],
            ['4040', 'T-Shirt Sales', 'income'],
            ['4050', 'Pledges', 'income'],
            ['4051', 'Specific Pledge Campaigns', 'income'],
            ['4060', 'Funerals', 'income'],
            ['4070', 'Youth Contributions', 'income'],
            ['4080', 'Building Fund', 'income'],
            ['4090', 'Solar Pledges', 'income'],
            ['4100', 'Missions', 'income'],
            ['4110', 'Welfare', 'income'],
            ['5000', 'Administration Expenses', 'expense'],
            ['5010', 'Worship Expenses', 'expense'],
            ['5020', 'Maintenance Expenses', 'expense'],
            ['5030', 'Outreach and Missions Expenses', 'expense'],
            ['5040', 'Welfare Expenses', 'expense'],
            ['5050', 'Funeral Assistance Expenses', 'expense'],
        ];

        foreach ($accounts as [$code, $name, $type]) {
            ChartAccount::updateOrCreate(
                ['code' => $code],
                ['name' => $name, 'type' => $type, 'status' => 'active']
            );
        }
    }
}
