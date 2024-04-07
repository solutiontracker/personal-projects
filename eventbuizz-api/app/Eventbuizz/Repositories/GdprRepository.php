<?php
namespace App\Eventbuizz\Repositories;

use App\Mail\Email;
use Illuminate\Http\Request;

class GdprRepository extends AbstractRepository
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param mixed $event_id
     * @param mixed $attendee_id
     *
     * @return [type]
     */
    public static function sendGdprEmail($event_id, $attendee_id)
    {
        $event = \App\Models\Event::where('id', $event_id)->first();

        $attendee = \App\Models\Attendee::where('id', $attendee_id)->whereNull('deleted_at')->first();

        $templateData = \App\Models\EventEmailTemplate::where('event_id', $event_id)
            ->where('alias', '=', 'gdpr')->where('type', '=', 'email')->with(['info' => function ($q) use ($event) {
            $q->where('languages_id', $event->language_id);
        }])->get()->toArray();

        $template = $subject = $alias = "";

        if (count($templateData) > 0) {
            foreach ($templateData[0]['info'] as $info) {
                $alias = $templateData['email_template']['alias'];
                if ($info['name'] == 'template') {
                    $template = $info['value'];
                }
                if ($info['name'] == 'subject') {
                    $subject = $info['value'];
                }
            }

            $event_setting = EventSettingRepository::getEventSetting(["event_id" => $event_id, "language_id" => $event->language_id]);

            if ($event_setting['header_logo'] != '' && $event_setting['header_logo'] != 'NULL') {
                $src = cdn('/assets/event/branding/' . $event_setting['header_logo']);
            } else {
                $src = cdn("/_admin_assets/images/eventbuizz_logo.png");
            }

            $logo = '<img src="' . $src . '" width="150" />';

            $subject = str_replace("{event_name}", stripslashes($event['name']), $subject);
            $template = getEmailTemplate($template, $event_id);
            $content = stripslashes($template);
            $content = str_replace("{event_logo}", stripslashes($logo), $content);
            $content = str_replace("{event_name}", stripslashes($event['name']), $content);
            $content = str_replace("{event_organizer_name}", "" . $event['organizer_name'], $content);
            $name = stripslashes($attendee['first_name'] . ' ' . $attendee['last_name']);
            $content = str_replace("{attendee_name}", stripslashes($name), $content);
            $content = str_replace("{attendee_email}", stripslashes($attendee['email']), $content);
            $content = str_replace("{date}", date('Y-m-d'), $content);
            $content = str_replace("{time}", date('H:i:s'), $content);

            $gdpr_settings = \App\Models\EventGdprSetting::where('event_id', $event_id)->whereNull('deleted_at')->first();
            $bcc_emails = $gdpr_settings->bcc_emails;
            $emails = explode(',', $bcc_emails);

            $data = array();
            $data['event_id'] = $event->id;
            $data['template'] = $alias;
            $data['subject'] = $subject;
            $data['content'] = $content;
            $data['view'] = 'email.plain-text';
            $data['from_name'] = $event_setting['organizer_name'];
            if($bcc_emails) \Mail::to($emails)->send(new Email($data));
        }
    }
}
