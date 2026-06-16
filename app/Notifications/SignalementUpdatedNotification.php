<?php

namespace App\Notifications;

use App\Models\Signalement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SignalementUpdatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Signalement $signalement,
        public string $message,
        public string $type = 'update',
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->signalement->loadMissing('category');

        return (new MailMessage)
            ->subject('Mise à jour — '.$this->signalement->reference)
            ->greeting('Bonjour '.$notifiable->name.',')
            ->line($this->message)
            ->line('Signalement : '.$this->signalement->reference)
            ->line('Catégorie : '.$this->signalement->category->name)
            ->action('Voir le signalement', route('signalement.show', $this->signalement->reference))
            ->salutation('Smart Emergency AI — Niger');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->type,
            'reference' => $this->signalement->reference,
            'message' => $this->message,
            'url' => route('signalement.show', $this->signalement->reference),
        ];
    }
}
