<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;

use App\Mail\Email;

class HubAdministratorRepository extends AbstractRepository
{
    public function __construct(Request $request)
    {
        $this->formInput = $request;
    }
    
    /**
     * sendEmail
     *
     * @param  mixed $id
     * @param  mixed $event_id
     * @param  mixed $type_id
     * @param  mixed $type
     * @param  mixed $organizer_id
     * @return void
     */
    public static function sendEmail($id, $event_id, $type_id, $type, $organizer_id = null)
    {
        $user_detail = \App\Models\HubAdministrator::find($id);
        $organizer = \App\Models\Organizer::find($organizer_id);
        $template = \App\Models\HubOrganizerEmailTemplate::where('organizer_id', '=', $organizer_id)->where('alias', '=', 'hub_template')->whereNull('deleted_at')->first();
        $event = \App\Models\Event::find($event_id);
        if ($type == 'sponsor') {
            $sponsor = \App\Models\EventSponsor::where('id', '=', $type_id)->whereNull('deleted_at')->first();
        } else {
            $exhibitor = \App\Models\EventExhibitor::where('id', '=', $type_id)->whereNull('deleted_at')->first();
        }
        $event_setting  = get_event_branding($event_id);
		if (isset($event_setting['header_logo']) && $event_setting['header_logo']) {
			$src = cdn('/assets/event/branding/' . $event_setting['header_logo']);
		} else {
			$src = cdn('/_admin_assets/images/eventbuizz_logo.png');
		}
		$logo = '<img src="' . $src . '" width="150" />';
        $subject = $template['subject'];
        $subject = str_replace("{event_name}", $event->name, $subject);
        $body = $template['template'];
        $body = getEmailTemplate($body, $event_id);
        $body = stripslashes($body);
        $body = str_replace("{event_name}", $event->name, $body);
        $body = str_replace("{event_organizer_name}", "" . $event->organizer_name, $body);
        $body = str_replace("{hub_admin_name}", "" . $user_detail->first_name . ' ' . $user_detail->last_name, $body);
        $body = str_replace("{sponsor_name}", "" . $sponsor->name, $body);
        $body = str_replace("{exhibitor_name}", "" . $exhibitor->name, $body);
        $body = str_replace("{event_logo}", $src, $body);
        $body = str_replace("{link}", cdn('/_hub/' . $organizer->user_name . '/login'), $body);
        $body = str_replace("{login_link}", cdn('/_hub/' . $organizer->user_name . '/login'), $body);

        $data = array();
		$data['event_id'] = $event->id;
		$data['organizer_id'] = $event->organizer_id;
		$data['template'] = $alias;
		$data['subject'] = $subject;
		$data['content'] = $body;
		$data['view'] = 'email.plain-text';
		$data['from_name'] =  $user_detail->first_name;
		\Mail::to($user_detail->email)->send(new Email($data));
    }
}
