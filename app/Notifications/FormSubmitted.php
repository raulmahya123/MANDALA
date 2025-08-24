<?php

namespace App\Notifications;

use App\Models\FormEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FormSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public FormEntry $entry) {}

    public function via(object $notifiable): array
    {
        return ['mail','database']; // bisa 'slack' atau 'broadcast' juga
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Form Baru Disubmit: '.$this->entry->form->title)
            ->greeting('Halo '.$notifiable->name.',')
            ->line('Ada form baru yang dikirim oleh '.$this->entry->user->name.'.')
            ->line('Judul Form: '.$this->entry->form->title)
            ->action('Tinjau Form', route('form.entry.show',$this->entry))
            ->line('Terima kasih.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'entry_id' => $this->entry->id,
            'form_title' => $this->entry->form->title,
            'user' => $this->entry->user->name,
            'status' => $this->entry->status,
        ];
    }
}
