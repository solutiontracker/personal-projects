<?php

namespace App\Http\Controllers\Auth\Lead;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Mail\Email;
use Illuminate\Support\Carbon;
class RegisterController extends Controller
{
    protected $successStatus = 200;
    
    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function store(Request $request)
    {
        if($request['mode'] !== "lead_user")
        {
            return response()->json([
                'status' => 0, 
                'response_type' => INVALID_USER_MODE, 
                'message' => "Server setting has been changed please export your leads records and logout" 
            ], $this->successStatus);
        }
        $validateAccessCode = $this->validateAccessCode($request['access_code'], $request['event_id']);
        if($validateAccessCode['status'] !== 1)
        {
            return response()->json($validateAccessCode, $this->successStatus);
        }
        $validateEmail = $this->validateEmail($request['email'], $request['event_id']);
        
        if($validateEmail['status'] !== 1)
        {
            return response()->json($validateEmail, $this->successStatus);
        }

        $getOrganizerApproval  =  $this->getLeadSetting($request['event_id'],'enable_organizer_approval');

        $newUser = \App\Models\LeadUser::create([
            "event_id"=> $request['event_id'],
            "name"=> $request['name'],
            "email"=> $request['email'],
            "phone" => $request['phone'],
            "password"=> \Hash::make($request['password']),
            "status" => $getOrganizerApproval == 1 ? 0 : 1,
            "approved" => $getOrganizerApproval == 1 ? 0 : 1,
        ]);

        if($getOrganizerApproval == 1){
            $this->sendSignupRequestEmail($request['event_id'], $newUser);
        }

        return response()->json(['status' => 1, 'data' => ["user" => $newUser], 'message' => "User was successfully created" ], $this->successStatus);
    }
    
    /**
     * validateAccessCode
     *
     * @param  mixed $accessCode
     * @param  mixed $event_id
     * @return void
     */
    public function validateAccessCode($accessCode, $event_id)
    {   
        $leadSettings = \App\Models\LeadSetting::select('access_code')->where('event_id', $event_id)->pluck('access_code')->first();
        if((string) $accessCode ===  $leadSettings){
            return ['status' => 1, 'message' => "success" ];
        }
        return [
            'status' => 0, 
            'response_type' => INVALID_EVENT_ACCESS_CODE, 
            'message' => "Invalid Access Code" 
        ];
    }
    
    /**
     * validateEmail
     *
     * @param  mixed $email
     * @param  mixed $event_id
     * @return void
     */
    public function validateEmail($email, $event_id)
    {
        $id = \App\Models\LeadUser::select('id')->where('event_id', $event_id)->where('email', $email)->pluck('id')->first();
        if(!is_null($id))
        {
            return [
                'status' => 0, 
                'response_type' => LEAD_USER_EMAIL_ALREADY_REGISTERED, 
                'message' => "Email Already Registered" 
            ];
        }
        return ['status' => 1, 'message' => "Success" ];    
    }
    
    /**
     * getLeadSetting
     *
     * @param  mixed $event_id
     * @param  mixed $column
     * @return void
     */
    public function getLeadSetting($event_id, $column)
    {
        $setting = \App\Models\LeadSetting::select($column)->where('event_id', $event_id)->pluck($column)->first();
        return $setting;
    }

    public function sendSignupRequestEmail($event_id, $user)
    {
      
        $event = \App\Models\Event::find($event_id);
        $language_id = $event['language_id'];
        $email_template = \App\Models\EventEmailTemplate::where('event_id', '=', $event_id)
            ->where('alias', 'leads_user_request')->where('type', 'email')->with(['info' => function ($q) use ($language_id) {
                $q->where('languages_id', '=', $language_id);
            }])->get()->toArray();
        $template = '';
        $subject_template = '';
        foreach ($email_template[0]['info'] as $info) {
            if ($info['name'] == 'template') {
                $template = $info['value'];
            }
            if ($info['name'] == 'subject') {
                $subject_template = $info['value'];
            }
        }
        $subject = str_replace("{event_name}", stripslashes($event['name']), $subject_template);
        $to_email = \App\Models\EventInfo::where("event_id", $event['id'])->where("name", "support_email")->pluck('value')->first();

        $contents = getEmailTemplate($template, $event_id);
        $contents = $this->emailReplaceTags($event, 'lead_user', "", 0, $user, $contents);
        
        $data['email'] = $to_email;
        $data['from_name'] = $event['organizer_name'];
        $data['event_id'] = $event['id'];
        $data['subject'] = $subject;
        $data['content'] = $contents;
        $data['view'] = 'email.plain-text';
        $data['from_name'] = $event['organizer_name'];
        \Mail::to($to_email)->send(new Email($data));
    }

    public function emailReplaceTags($event, $mode, $type_name, $type_id, $contact_person_detail, $contents)
    {
        $event_settings_logo = \App\Models\EventSetting::where("event_id", $event['id'])->where("name", "header_logo")->pluck('value')->first();
        $event_settings_color = \App\Models\EventSetting::where("event_id", $event['id'])->where("name", "primary_color")->pluck('value')->first();

        if ($event_settings_logo != '' && $event_settings_logo != 'NULL') {
            $event_image = config('app.eventcenter_url') . '/assets/event/branding/' . $event_settings_logo;
        } else {
            $event_image = config('app.eventcenter_url') . "/_admin_assets/images/eventbuizz_logo.png";
        }

        //Getting Primary Color
        $primary_color = '#f28121';
        if (trim($event_settings_color != '')) {
            $primary_color = $event_settings_color;
        }

        $profile_logo = "";
        if($mode === "contact_person"){
            if($type_name === "sponsor"){
                $sponsor = \App\Models\EventSponsor::select(['logo'])->where('id', $type_id)->first(); 
                $image_path = config('app.eventcenter_url') . '/assets/sponsors/' . $sponsor->logo;
                $profile_logo = $sponsor->logo !== "" ? "<img width='250' height='85' src='$image_path' />" : "";
            }
            elseif($type_name === "exhibitor"){
                $exhibitor = \App\Models\EventExhibitor::select(['logo'])->where('id', $type_id)->first(); 
                $image_path = config('app.eventcenter_url') . '/assets/exhibitors/' . $exhibitor->logo;
                $profile_logo = $exhibitor->logo !== "" ? "<img width='250' height='85' src='$image_path' />" : "";
            }
        }

        $attendee_name = $contact_person_detail['first_name'] . ' ' . $contact_person_detail['last_name'];
        $contents = str_replace("{event_logo}", '<img width="250" height="85" src="' . $event_image . '" />', $contents);
        $contents = str_replace("{attendee_name}", $attendee_name, $contents);
        $contents = str_replace("{event_name}", $event->name, $contents);
        $contents = str_replace("{event_organizer_name}", $event->organizer_name, $contents);
        $contents = str_replace("{primary_color_background}", 'background:' . $primary_color, $contents);
        $contents = str_replace("{notes}", ' ', $contents);

        $lead_user_name = $contact_person_detail['name'] ?? "";
        $lead_user_first_name = $contact_person_detail['first_name'] ?? $lead_user_name;
        $lead_user_last_name = $contact_person_detail['last_name'] ?? "";
        $lead_user_email = $contact_person_detail['email'] ?? "";

        $contact_person_first_name = $contact_person_detail['first_name'] ?? $lead_user_name;
        $contact_person_last_name = $contact_person_detail['last_name'] ?? "";
        $contact_person_email = $contact_person_detail['email'] ?? "";
        $contact_person_phone_number = $contact_person_detail['phone'] ?? "";
        $contact_person_company = $contact_person_detail['info']['company_name'] ?? "";
        $contact_person_title = $contact_person_detail['info']['title'] ?? "";
        $products = $contact_person_detail['products'] ?? "";
        $consents = $contact_person_detail['consents'] ?? "";

        $carbon_date = Carbon::now();
        $date = $carbon_date->format('Y-m-d');
        $time = $carbon_date->format('H:i:s');

        $module_name = "Leads Managment";


        $contents = str_replace("{lead_user_first_name}", $lead_user_first_name, $contents);
        $contents = str_replace("{lead_user_last_name}", $lead_user_last_name, $contents);
        $contents = str_replace("{lead_user_email}", $lead_user_email, $contents);
        $contents = str_replace("{exhibitor_/_sponsor_logo}", $profile_logo, $contents);
        $contents = str_replace("{contact_person_first_name}", $contact_person_first_name, $contents);
        $contents = str_replace("{contact_person_last_name}", $contact_person_last_name, $contents);
        $contents = str_replace("{contact_person_email}", $contact_person_email, $contents);
        $contents = str_replace("{contact_person_phone_number}", $contact_person_phone_number, $contents);
        $contents = str_replace("{contact_person_company}", $contact_person_company, $contents);
        $contents = str_replace("{contact_person_title}", $contact_person_title, $contents);
        $contents = str_replace("{products}", $products, $contents);
        $contents = str_replace("{products_list}", $products, $contents);
        $contents = str_replace("{consents}", $consents, $contents);
        $contents = str_replace("{date}", $date, $contents);
        $contents = str_replace("{time}", $time, $contents);
        $contents = str_replace("{module_name}", $module_name, $contents);

        return $contents;
    }
}
