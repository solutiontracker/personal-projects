<?php

namespace App\Notifications\Auth\Lead;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorAuthentication extends Notification
{
    use Queueable;

    private $params;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($params)
    {
        $this->params = $params;
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
        $event = $this->params['event'];
        $user = $this->params['user'];
        $mode = $this->params['mode'];
        $authentication = $this->params['authentication'];
        $sms_organizer_name = isset($event['organizer_name']) ? $event['organizer_name'] : '';
        $user_name = $mode === "lead_user" ? $user->name : $user->first_name . ' ' . $user->last_name;
        $first_name = $mode === "lead_user" ? $user->name : $user->first_name;
        $last_name = $mode === "lead_user" ? '' : $user->last_name;
        $template = getTemplate('email', 'native_app_reset_password', $event['id'], $event['language_id']);

        $event_setting = get_event_branding($event['id']);
        if (isset($event_setting['header_logo']) && $event_setting['header_logo']) {
            $src = cdn('/assets/event/branding/' . $event_setting['header_logo']);
        } else {
            $src = cdn('/_admin_assets/images/eventbuizz_logo.png');
        }

        $logo = '<img src="' . $src . '" width="150" />';

        $subject = $template->info[0]['value'];
        $subject = str_replace("{event_name}", $event['name'], $subject);
        $body = $template->info[1]['value'];
        $body = getEmailTemplate($body, $event['id']);
        $body = stripslashes($body);
        $body = str_replace("{event_name}", $event['name'], $body);
        $body = str_replace("{event_organizer_name}", "" . $event->organizer_name, $body);
        $body = str_replace("{code}", $authentication->token, $body);
        $body = str_replace("{password}", $authentication->token, $body);
        $body = str_replace("{event_logo}", $logo, $body);
        $body = str_replace("{attendee_name}", $user_name , $body);

        $body = str_replace("{first_name}", $first_name , $body);
        $body = str_replace("{last_name}", $last_name , $body);

        return (new MailMessage)
            ->subject($subject)
            ->from('donotreply@eventbuizz.com', $sms_organizer_name)
            ->replyTo('donotreply@eventbuizz.com', $sms_organizer_name)
            ->view(
                'email.html', ['html' => $body]
            );
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
