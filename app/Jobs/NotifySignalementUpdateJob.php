<?php

namespace App\Jobs;

use App\Models\Signalement;
use App\Models\User;
use App\Notifications\SignalementUpdatedNotification;
use Illuminate\Foundation\Queue\Queueable;

class NotifySignalementUpdateJob
{
    use Queueable;

    public function __construct(
        public int $signalementId,
        public string $message,
        public string $type = 'update',
    ) {}

    public function handle(): void
    {
        $signalement = Signalement::with('user')->find($this->signalementId);

        if (! $signalement?->user) {
            return;
        }

        $signalement->user->notify(new SignalementUpdatedNotification(
            $signalement,
            $this->message,
            $this->type,
        ));
    }
}
