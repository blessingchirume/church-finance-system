<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Service;
use App\Models\TuckshopSale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceBalanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_balances_are_computed_from_previous_service_and_transactions(): void
    {
        $firstService = Service::create([
            'service_date' => '2026-07-05',
            'description' => 'First Sunday service',
        ]);
        $secondService = Service::create([
            'service_date' => '2026-07-12',
            'description' => 'Second Sunday service',
        ]);

        Income::create([
            'service_id' => $firstService->id,
            'type' => 'offering',
            'amount' => 100,
            'description' => 'Offering',
        ]);
        TuckshopSale::create([
            'service_id' => $firstService->id,
            'external_reference' => 'TS-001',
            'amount' => 25,
        ]);
        Expense::create([
            'service_id' => $firstService->id,
            'amount' => 40,
            'category' => 'worship',
            'description' => 'Worship expense',
        ]);

        Income::create([
            'service_id' => $secondService->id,
            'type' => 'offering',
            'amount' => 50,
            'description' => 'Offering',
        ]);

        $firstService->refresh();
        $secondService->refresh();

        $this->assertSame('0.00', $firstService->opening_balance);
        $this->assertSame('85.00', $firstService->closing_balance);
        $this->assertSame('85.00', $secondService->opening_balance);
        $this->assertSame('135.00', $secondService->closing_balance);
    }

    public function test_service_form_does_not_require_manual_balances(): void
    {
        $this->withoutMiddleware()
            ->actingAs(\App\Models\User::factory()->create())
            ->post(route('services.store'), [
                'service_date' => '2026-07-05',
                'description' => 'Sunday service',
            ])
            ->assertRedirect(route('services.index'));

        $this->assertDatabaseHas('services', [
            'description' => 'Sunday service',
            'opening_balance' => 0,
            'closing_balance' => 0,
        ]);
    }
}
