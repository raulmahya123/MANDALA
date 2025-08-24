<?php

namespace App\Notifications;

use App\Models\FormEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FormReviewed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public FormEntry $entry,
        public string $action // 'approved' atau 'rejected'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $status = strtoupper($this->action);
        return (new MailMessage)
            ->subject("Form {$status}: ".$this->entry->form->title)
            ->greeting('Halo '.$notifiable->name.',')
            ->line('Form yang Anda submit sudah ditinjau.')
            ->line('Status: '.$status)
            ->action('Lihat Detail', route('form.entry.show',$this->entry))
            ->line('Terima kasih.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'entry_id' => $this->entry->id,
            'form_title' => $this->entry->form->title,
            'status' => $this->action,
        ];
    }
}
