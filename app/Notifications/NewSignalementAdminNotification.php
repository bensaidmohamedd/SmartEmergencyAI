<?php

namespace App\Notifications;

use App\Models\Signalement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSignalementAdminNotification extends Notification
{
    use Queueable;

    public function __construct(public Signalement $signalement) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $this->signalement->loadMissing('category');

        return (new MailMessage)
            ->subject('Nouvelle urgence — '.$this->signalement->reference)
            ->greeting('Alerte opérationnelle')
            ->line('Nouveau signalement : '.$this->signalement->category->name)
            ->line('Priorité IA : '.($this->signalement->ai_priority_rank ?? '—').'/100')
            ->line('Crédibilité : '.($this->signalement->ai_credibility_score ?? '—').'/100')
            ->action('Gérer', route('admin.signalements.show', $this->signalement->reference))
            ->salutation('Smart Emergency AI — Niger');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_signalement',
            'reference' => $this->signalement->reference,
            'message' => 'Nouvelle urgence '.$this->signalement->reference.' — priorité '.($this->signalement->ai_priority_rank ?? '—').'/100',
            'url' => route('admin.signalements.show', $this->signalement->reference),
        ];
    }
}
