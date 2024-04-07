<?php

namespace App\Notifications\Auth\Organizer;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Route;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;
    public $name;

    private $params;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token, $name = '')
    {
        $this->token = $token;
        $this->name = $name;
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
        $token = rand(100000, 999999);

        //update token length
        \DB::table('conf_password_resets')->where('email', $notifiable->email)->orderBy('created_at', 'DESC')->update([
            "token" => \Hash::make($token)
        ]);

        return (new MailMessage)
            ->subject(__('wizard.auth.reset_password_email_subject'))
            ->line('<div class="test-text" style="margin: 0px; font-family: Open Sans,Helvetica,  Arial, sans-serif; font-size: 14px; color: #989898; line-height: 18px; text-align: center;" data-mce-style="margin: 0px; font-family: Open Sans,Helvetica,  Arial, sans-serif; font-size: 14px; color: #989898; line-height: 18px; text-align: center;">' . __('wizard.auth.reset_password_email_template_description') . '</div>')
            ->line('<div style="margin: 0px; font-family: Open Sans,Helvetica, Arial, sans-serif; font-size: 14px; color: #21b5d5; line-height: 18px; text-align: center; letter-spacing: 2px;" data-mce-style="margin: 0px; font-family: Open Sans,Helvetica, Arial, sans-serif; font-size: 14px; color: #21b5d5; line-height: 18px; text-align: center; letter-spacing: 2px;">' . $token . '</div>')
            //->action("Reset Password", route('wizard-auth-reset', ['token' => $this->token]))
            ->greeting(__('wizard.auth.reset_password_email_template_greeting') . " " . $notifiable->first_name . " " . $notifiable->last_name . "\n")
            ->salutation(__('wizard.auth.reset_password_email_template_salutation') . "<br>" . __('wizard.auth.reset_password_email_template_salutation_team'));
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
