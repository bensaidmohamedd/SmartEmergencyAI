<?php

namespace App\Http\Controllers\Admin;

use App\Models\PlatformStat;
use App\Services\AuditLogger;
use App\Services\PlatformStatsCalculator;
use Illuminate\Http\Request;

class AdminPlatformStatController extends AdminController
{
    public function index()
    {
        return view('admin.platform-stats.index', [
            'layout' => 'admin',
            'user' => $this->adminUser()->toViewArray(),
            'stats' => PlatformStat::orderBy('id')->get(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'stats' => ['required', 'array'],
            'stats.*.id' => ['required', 'exists:platform_stats,id'],
            'stats.*.value' => ['required', 'string', 'max:255'],
            'stats.*.label' => ['required', 'string', 'max:255'],
        ]);

        foreach ($validated['stats'] as $statData) {
            PlatformStat::where('id', $statData['id'])->update([
                'value' => $statData['value'],
                'label' => $statData['label'],
            ]);
        }

        AuditLogger::log('platform_stats.update');

        return back()->with('success', 'Statistiques de la plateforme mises à jour.');
    }

    public function sync()
    {
        PlatformStatsCalculator::sync();
        AuditLogger::log('platform_stats.sync');

        return back()->with('success', 'Statistiques recalculées depuis les données réelles.');
    }
}
