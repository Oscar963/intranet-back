<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{

    public $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $resetUrl = config('app.reset_password_url') . '?token=' . $this->token . '&email=' . urlencode($notifiable->email) . '&rut=' . urlencode($notifiable->rut);
        $logoUrl = secure_asset('assets/logos/logo.png');

        return (new MailMessage)
            ->subject('Restablecimiento de contraseña')
            ->line('Está recibiendo este correo porque recibimos una solicitud de restablecimiento de contraseña para su cuenta.')
            ->action('Restablecer contraseña', $resetUrl)
            ->line('Este enlace de restablecimiento de contraseña expirará en :count minutos.', ['count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire')])
            ->line('Si no solicitó un restablecimiento de contraseña, no se requiere ninguna acción adicional.')
            ->view('emails.reset_password', [
                'resetUrl' => $resetUrl,
                'logoUrl' => $logoUrl
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
