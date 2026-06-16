<?php

namespace Tests\Unit;

use App\Services\EmergencyAnalysisService;
use PHPUnit\Framework\TestCase;

class EmergencyAnalysisServiceTest extends TestCase
{
    private EmergencyAnalysisService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new EmergencyAnalysisService;
    }

    public function test_rejects_obvious_fake_declaration(): void
    {
        $result = $this->service->analyze(
            'Incendie',
            "C'est un test, ya rien du tout lol",
            null,
            null,
            null,
            13.5127,
            2.1124,
        );

        $this->assertSame(EmergencyAnalysisService::VERDICT_REJECTED, $result['verdict']);
        $this->assertFalse($result['can_submit']);
        $this->assertNotEmpty($result['rejection_reasons']);
    }

    public function test_approves_credible_fire_emergency(): void
    {
        $result = $this->service->analyze(
            'Incendie',
            'Gros incendie dans un immeuble au centre-ville, fumée dense visible, plusieurs personnes bloquées au 3e étage.',
            true,
            'dense',
            'habitation',
            13.5127,
            2.1124,
            true,
        );

        $this->assertContains($result['verdict'], [
            EmergencyAnalysisService::VERDICT_APPROVED,
            EmergencyAnalysisService::VERDICT_REVIEW,
        ]);
        $this->assertTrue($result['can_submit']);
        $this->assertGreaterThanOrEqual(55, $result['credibility_score']);
        $this->assertGreaterThan(50, $result['priority_rank']);
    }

    public function test_rejects_gps_outside_niger(): void
    {
        $result = $this->service->analyze(
            'Accident',
            'Accident de voiture sur l\'autoroute, deux véhicules impliqués, blessés sur place.',
            null,
            null,
            null,
            48.8566,
            2.3522,
        );

        $this->assertSame(EmergencyAnalysisService::VERDICT_REJECTED, $result['verdict']);
        $this->assertContains('position GPS hors du Niger', $result['rejection_reasons']);
    }

    public function test_flags_incoherent_category_for_review_or_reject(): void
    {
        $result = $this->service->analyze(
            'Incendie',
            'Mon chat a disparu dans le quartier, je ne le trouve plus depuis ce matin.',
            null,
            null,
            null,
            13.5127,
            2.1124,
        );

        $this->assertContains(
            'description incohérente avec la catégorie « Incendie »',
            $result['rejection_reasons']
        );
        $this->assertLessThan(65, $result['credibility_score']);
    }
}
