<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PeticionVencidaNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    
    private $peticion;

    public function __construct($peticion)
    {
        $this->peticion = $peticion;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Petición Vencida')
                    ->line('La petición "' . $this->peticion->tipo_peticion . '" ha vencido.')
                    ->action('Ver Petición', url('/peticiones/' . $this->peticion->id));
    }
}
