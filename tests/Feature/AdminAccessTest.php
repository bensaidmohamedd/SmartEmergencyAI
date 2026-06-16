<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Category::create(['name' => 'Incendie']);
    }

    public function test_citizen_cannot_access_admin(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_CITOYEN]);

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    }

    public function test_admin_can_access_new_sections(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)->get(route('admin.emergency-services.index'))->assertOk();
        $this->actingAs($admin)->get(route('admin.ai-review.index'))->assertOk();
    }

    public function test_citizen_can_access_services_and_quick_report(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_CITOYEN]);

        $this->actingAs($user)->get(route('services.index'))->assertOk();
        $this->actingAs($user)->get(route('report.quick'))->assertOk();
    }
}
