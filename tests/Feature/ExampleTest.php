<?php

namespace Tests\Feature;

use App\Models\PlatformStat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        PlatformStat::create(['key' => 'urgences_traitees', 'value' => '0', 'label' => 'Urgences traitées']);

        $this->get('/')->assertOk();
    }
}
