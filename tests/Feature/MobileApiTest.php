<?php

namespace Tests\Feature;

use App\Models\Assembly;
use App\Models\ChartAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_finance_user_can_login_and_receive_assigned_assemblies(): void
    {
        $assembly = Assembly::create([
            'name' => 'Eastview Assembly',
            'code' => 'EAST',
            'status' => 'active',
        ]);
        $user = User::factory()->create(['role' => 'treasurer']);
        $user->assemblies()->attach($assembly);

        $response = $this->postJson('/api/mobile/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'Treasurer Phone',
        ]);

        $response->assertOk()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('assemblies.0.id', $assembly->id);
    }

    public function test_mobile_user_can_submit_income_transaction_and_list_recent_records(): void
    {
        $assembly = Assembly::create([
            'name' => 'Eastview Assembly',
            'code' => 'EAST',
            'status' => 'active',
        ]);
        $account = ChartAccount::create([
            'code' => '4010',
            'name' => 'Offerings',
            'type' => 'income',
            'status' => 'active',
        ]);
        $user = User::factory()->create(['role' => 'treasurer']);
        $user->assemblies()->attach($assembly);

        $token = $this->postJson('/api/mobile/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->json('token');

        $submit = $this->withToken($token)->postJson('/api/mobile/transactions', [
            'mobile_client_id' => 'phone-transaction-001',
            'assembly_id' => $assembly->id,
            'date' => '2026-07-05',
            'type' => 'income',
            'flow' => 'offerings',
            'chart_account_id' => $account->id,
            'category_purpose' => 'Sunday morning offering',
            'amount' => 125.75,
            'currency' => 'USD',
            'payment_method' => 'cash',
            'notes' => 'Captured after service.',
        ]);

        $submit->assertCreated()
            ->assertJsonPath('data.record_type', 'income')
            ->assertJsonPath('data.status', 'approved');

        $this->assertDatabaseHas('incomes', [
            'assembly_id' => $assembly->id,
            'chart_account_id' => $account->id,
            'mobile_client_id' => 'phone-transaction-001',
            'submitted_from_mobile' => 1,
            'created_by' => $user->id,
            'approved_by' => $user->id,
            'status' => 'approved',
        ]);

        $this->withToken($token)
            ->getJson('/api/mobile/transactions/recent')
            ->assertOk()
            ->assertJsonPath('data.0.category_purpose', 'Sunday morning offering');
    }

    public function test_mobile_user_cannot_submit_for_unassigned_assembly(): void
    {
        $assigned = Assembly::create([
            'name' => 'Assigned Assembly',
            'code' => 'ASSIGNED',
            'status' => 'active',
        ]);
        $unassigned = Assembly::create([
            'name' => 'Unassigned Assembly',
            'code' => 'UNASSIGNED',
            'status' => 'active',
        ]);
        $account = ChartAccount::create([
            'code' => '4010',
            'name' => 'Offerings',
            'type' => 'income',
            'status' => 'active',
        ]);
        $user = User::factory()->create(['role' => 'treasurer']);
        $user->assemblies()->attach($assigned);

        $token = $this->postJson('/api/mobile/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->json('token');

        $this->withToken($token)->postJson('/api/mobile/transactions', [
            'assembly_id' => $unassigned->id,
            'date' => '2026-07-05',
            'type' => 'income',
            'flow' => 'offerings',
            'chart_account_id' => $account->id,
            'category_purpose' => 'Sunday offering',
            'amount' => 125.75,
            'currency' => 'USD',
            'payment_method' => 'cash',
        ])->assertForbidden();

        $this->assertDatabaseCount('incomes', 0);
    }
}
