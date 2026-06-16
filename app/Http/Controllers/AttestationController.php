<?php

namespace App\Http\Controllers;

use App\Models\Signalement;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttestationController extends Controller
{
    public function show(string $id)
    {
        $signalement = $this->findSignalement($id);

        return view('pages.attestation', [
            'layout' => 'guest',
            'signalement' => $signalement,
            'data' => $signalement->toViewArray(),
        ]);
    }

    public function download(string $id): StreamedResponse
    {
        $signalement = $this->findSignalement($id);
        $signalement->load(['category', 'user', 'assignedService']);

        $html = view('pages.attestation', [
            'layout' => 'guest',
            'signalement' => $signalement,
            'data' => $signalement->toViewArray(),
        ])->render();

        $filename = 'attestation-'.$signalement->reference.'.html';

        return response()->streamDownload(function () use ($html) {
            echo $html;
        }, $filename, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    private function findSignalement(string $id): Signalement
    {
        return Signalement::with(['category', 'user', 'assignedService', 'timelineSteps'])
            ->where('reference', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }
}
