<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;

class CheckInOutRepository extends AbstractRepository
{
    private $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     *EventCheckInSetting clone/default
     *
     * @param array
     */
    public function install($request)
    {
        $setting = \App\Models\EventCheckInSetting::where('event_id', $request['from_event_id'])->first();

        if ($setting) {
            $duplicate = $setting->replicate();
            $duplicate->event_id = $request['to_event_id'];
            $duplicate->save();
        } else {
            \App\Models\EventCheckInSetting::create([
                "event_id" => $request['to_event_id'],
                "status" => 1,
                "type" => 'multiple',
                "single_type" => 'per_event',
            ]);
        }
    }

    /**
     * @param mixed $url
     * @param null $event_id
     * @param null $attendee_id
     * @param null $organizer_id
     *
     * @return [type]
     */
    static public function insertURLShortner($url, $event_id = null, $attendee_id = null, $organizer_id = null)
    {
        do {

            $bytes = random_bytes(20);
            $uuid = bin2hex($bytes);   
            
        } while (\App\Models\URLShortner::where("uuid", $uuid)->first());

        $formInput['uuid'] = $uuid;
        $formInput['long_url'] = $url;
        $formInput['event_id'] = $event_id;
        $formInput['attendee_id'] = $attendee_id;
        $formInput['organizer_id'] = $organizer_id;
        $formInput = \App\Models\URLShortner::create($formInput);
        return $formInput;
    }

    /**
     * @param mixed $formInput
     *
     * @return [type]
     */
    static public function generateURlShortner($formInput)
    {
        $result = self::getURLShortner($formInput);
        if (!$result) {
            $url = urlencode($formInput['event_url'] . '/CheckinAttendee&attendee_id=' . $formInput['attendee_id'] . '&event_id=' . $formInput['event_id'] . '&organizer_id=' . $formInput['organizer_id']);
            $result = self::insertURLShortner($url, $formInput['event_id'], $formInput['attendee_id'], $formInput['organizer_id']);
            $url_code = $result->id;
            if($result->uuid !== null){
                $url_code = $result->uuid;
            }
            return cdn('/qr/?id=' . $url_code);
        } else {
            $url_code = $result->id;
            if($result->uuid !== null){
                $url_code = $result->uuid;
            }
            return cdn('/qr/?id=' . $url_code);
        }
    }

    /**
     * @param mixed $formInput
     *
     * @return [type]
     */
    static public function getURLShortner($formInput)
    {
        return \App\Models\URLShortner::where('event_id', $formInput['event_id'])
            ->where('attendee_id', $formInput['attendee_id'])->first();
    }

    /**
     * @param mixed $formInput
     *
     * @return [type]
     */
    public static function getSetting($formInput)
    {
        return \App\Models\EventCheckInSetting::where('event_id', $formInput['event_id'])->first();
    }

    /**
     * @param mixed $formInput
     *
     * @return [type]
     */
    public function getHistory($formInput)
    {
        return \App\Models\CheckInLog::where('event_id', $formInput['event_id'])
            ->where('attendee_id', $formInput['attendee_id'])
            ->with(['attendees.info' => function ($query) use ($formInput) {
                return $query->where('languages_id', $formInput['language_id'])->where('name', '=', 'company_name');
            }])
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * @param mixed $formInput
     *
     * @return [type]
     */
    public function perDayCheckInResults($formInput)
    {
        return \App\Models\CheckInLog::where('event_id', $formInput['event_id'])
            ->where('attendee_id', $formInput['attendee_id'])
            ->whereRaw("DATE_FORMAT(checkin,'%Y-%m-%d') = ?", [date('Y-m-d')])
            ->first();
    }

    /**
     * @param mixed $formInput
     *
     * @return [type]
     */
    public function perDayCheckOutResults($formInput)
    {
        return \App\Models\CheckInLog::where('event_id', $formInput['event_id'])
            ->where('attendee_id', $formInput['attendee_id'])
            ->orderBy('id', 'DESC')
            ->first();
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    public function save($formInput)
    {
        $event_id = $formInput['event_id'];
		$attendee_detail = $formInput['attendee_detail'];
        $attendee_id = $formInput['attendee_id'];
		$event = $formInput['event'];
		$organizer_id = $event['organizer_id'];
        $labels = eventsite_labels('checkIn', $formInput);
        $language_id = $event['language_id'];
        $event_time = getEventDateFormat($event_id, $language_id, 'mobile_site_checkin_popup_date_time', \Carbon\Carbon::now());
        $curent_date_time = \Carbon\Carbon::now();
        $Checkin = $labels['CHECKIN_MSG'];
        $Checkout = $labels['CHECKOUT_MSG'];
        $PerDay = $labels['Event_Type_Per_Day'];
		$PerEvent = $labels['Event_Type_Per_Event'];
		$check_in_log = \App\Models\CheckInLog::where('event_id', $formInput['event_id'])->where('attendee_id', $attendee_id)->first();
		$checkin_setting = $this->getSetting($formInput);
        if ($checkin_setting->type == "single") {
            // In case of #per_event only one time attendee can attend the event
            if ($checkin_setting->single_type == "per_event") {
                if ($check_in_log->checkin == "" || $check_in_log->checkout == '0000-00-00 00:00:00') {
                    if ($check_in_log->checkin == "") {
                        $data = array('event_id' => $event_id, 'organizer_id' => $organizer_id, 'attendee_id' => $attendee_id, 'type_id' => $event_id, 'type_name' => 'event', 'checkin' => date('Y-m-d H:i:s'), 'status' => '1', 'created_at' => $curent_date_time, 'updated_at' => $curent_date_time, 'delegate' => $attendee_detail['detail']['delegate_number']);
                        \App\Models\CheckInLog::create($data);
                        $json['message'] = "" . $attendee_detail["first_name"] . " " . $attendee_detail["last_name"] . " " . $Checkin . " " . $event_time;
                        $json['status'] = "checkin";
                    } else {
                        $update_array = array('checkout' => date('Y-m-d H:i:s'), 'updated_at' => $curent_date_time);
                        \App\Models\CheckInLog::where('event_id', '=', $event_id)->where('attendee_id', '=', $attendee_id)->where('checkin', '=', $check_in_log->checkin)->update($update_array);
                        $json['message'] = "" . $attendee_detail["first_name"] . " " . $attendee_detail["last_name"] . " " . $Checkout . " " . $event_time;
                        $json['status'] = "checkout";
                    }
                } else {
                    $json['message'] = "" . $attendee_detail["first_name"] . " " . $attendee_detail["last_name"] . " " . $PerEvent;
                }
            } else if ($checkin_setting->single_type == "per_day") {
                // In case of #per_day attendee cna attend event at one time in one day and next time on next day of event
                $perDayCheckInResults = $this->perDayCheckInResults($formInput);
                $perDayCheckOutResults = $this->perDayCheckOutResults($formInput);
                if ($perDayCheckInResults->checkin == "") {
                    $data = array('event_id' => $event_id, 'organizer_id' => $organizer_id, 'attendee_id' => $attendee_id, 'type_id' => $event_id, 'type_name' => 'event', 'checkin' => date('Y-m-d H:i:s'), 'status' => '1', 'created_at' => $curent_date_time, 'updated_at' => $curent_date_time, 'delegate' => $attendee_detail['detail']['delegate_number']);
                    \App\Models\CheckInLog::create($data);
                    $json['message'] = "" . $attendee_detail["first_name"] . " " . $attendee_detail["last_name"] . " " . $Checkin . " " . $event_time;
                    $json['status'] = "checkin";
                } else if ($perDayCheckOutResults->checkout == '0000-00-00 00:00:00') {
                    $update_array = array('checkout' => date('Y-m-d H:i:s'), 'updated_at' => $curent_date_time);
                    \App\Models\CheckInLog::where('event_id', '=', $event_id)->where('attendee_id', '=', $attendee_id)->where('checkin', '=', $perDayCheckInResults->checkin)->update($update_array);
                    $json['message'] = "" . $attendee_detail["first_name"] . " " . $attendee_detail["last_name"] . " " . $Checkout . " " . $event_time;
                    $json['status'] = "checkout";
                } else {
                    $json['message'] = "" . $attendee_detail["first_name"] . " " . $attendee_detail["last_name"] . " " . $PerDay;
                }
            }
        } else {
            // In case of Multiple
            //in Multiple
			$perDayCheckOutResults = $this->perDayCheckOutResults($formInput);
            if ($perDayCheckOutResults->checkout != '0000-00-00 00:00:00') {
                // "multiple check in";
                $data = array('event_id' => $event_id, 'organizer_id' => $organizer_id, 'attendee_id' => $attendee_id, 'type_id' => $event_id, 'type_name' => 'event', 'checkin' => date('Y-m-d H:i:s'), 'status' => '1', 'created_at' => $curent_date_time, 'updated_at' => $curent_date_time, 'delegate' => $attendee_detail['detail']['delegate_number']);
                \App\Models\CheckInLog::create($data);
                $json['message'] = "" . $attendee_detail["first_name"] . " " . $attendee_detail["last_name"] . " " . $Checkin . " " . $event_time;
                $json['status'] = "checkin";
            } else {
                // "multiple check out";
                $update_array = array('checkout' => date('Y-m-d H:i:s'), 'updated_at' => $curent_date_time);
                \App\Models\CheckInLog::where('event_id', '=', $event_id)->where('attendee_id', '=', $attendee_id)->where('checkin', '=', $perDayCheckOutResults->checkin)->update($update_array);
                $json['message'] = "" . $attendee_detail["first_name"] . " " . $attendee_detail["last_name"] . " " . $Checkout . " " . $event_time;
                $json['status'] = "checkout";
            }
        }

        return $json;
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    public function checkInOutProgram($formInput)
    {
        $setting = $this->getSetting(['event_id' => $formInput['event_id']]);

        $admin_id = 0;

        $language_id = $formInput['language_id'];

        $filter_alias = 'program';

        $organizer_id = $formInput['organizer_id'];

        $event_id = $formInput['event_id'];

        $attendee_id = $formInput['attendee_id'];

        $program_id = $formInput['program_id'];

		$attendee_detail = $formInput['attendee_detail'];

        $labels = eventsite_labels('checkIn', $formInput);

        if($setting->validate_program_checkin == '1') {

            $attendee_program = \App\Models\EventAgendaAttendeeAttached::where('attendee_id', $attendee_id)->where('agenda_id', $program_id)->first();

            if(!$attendee_program) {

                $program_setting = ProgramRepository::getSetting(['event_id' => $formInput['event_id']]);

                if($program_setting->enable_program_attendee == '1') {

                    $common_groups = \App\Models\EventAttendeeGroup::join('conf_event_agenda_groups', "conf_event_attendees_groups.group_id", "=", "conf_event_agenda_groups.group_id")->where('conf_event_attendees_groups.attendee_id', $attendee_id)->where("conf_event_agenda_groups.agenda_id" , $program_id)->first();
                   
                    if(!$common_groups){
                        return array(
                            'data' => [
                                'success'=> false, 'message'=>'You are not attached to this program'
                            ]
                        );
                    }

                }
                else {

                    return array(
                        'data' => [
                            'success'=> false, 'message'=>'You are not attached to this program'
                        ]
                    );

                }

            } else {

                if($setting->program_checkin == 0) {

                    return array(
                        'data' => [
                            'success'=> false, 'message'=>'You cannot checkin in this program'
                        ]
                    );
                }

            }

        }

        if($setting && $setting->parallel_session_check_in == 1 && $setting->program_checkin == 1 && $filter_alias == "program") {

            $ids = \App\Models\EventAgenda::select('id')->where('id', '!=' , $program_id)->where("event_id", $event_id)->where('show_program_on_check_in_app', 1)->get();
            
            $delegate = \App\Models\AttendeeInfo::where('attendee_id', $attendee_id)->where('languages_id', $language_id)->where('name', 'delegate_number')->first();

            foreach ($ids as $id) {

                $id = $id['id'];

                if($setting->validate_program_checkin == 1) {

                    $attached = \App\Models\EventAgendaAttendeeAttached::where("attendee_id", $attendee_id)->where("agenda_id", $id)->first();

                    if($attached) {
                        $checkInOutTime = $this->saveCheckInOut($event_id, $organizer_id, $attendee_id, $delegate, $filter_alias, $id, $formInput['checkin_status']);
                    }

                } else {
                    $checkInOutTime =  $this->saveCheckInOut($event_id, $organizer_id, $attendee_id, $delegate, $filter_alias, $id, $formInput['checkin_status']);
                }
            }

        } else {
            $checkInOutTime = $this->saveCheckInOut($event_id, $organizer_id, $attendee_id, $delegate, $filter_alias, $program_id, $formInput['checkin_status']);
        }

        $Checkin = $labels['CHECKIN_MSG'];
        $Checkout = $labels['CHECKOUT_MSG'];

        $msg = $formInput['checkin_status'] == 1 ? $Checkin : $Checkout;
        
        return array(
            "data" => [
                'success'=> true,
                'message'=>"" . $attendee_detail["first_name"] . " " . $attendee_detail["last_name"] . " " . $msg . " " . $checkInOutTime
            ]
        );

    }
    
    /**
     * saveCheckInOut
     *
     * @param  mixed $event_id
     * @param  mixed $organizer_id
     * @param  mixed $attendee_id
     * @param  mixed $delegate
     * @param  mixed $filter_alias
     * @param  mixed $id
     * @param  mixed $type
     * @return void
     */
    public function saveCheckInOut($event_id, $organizer_id, $attendee_id, $delegate, $filter_alias, $id, $checkin_status)
    {
        $log = \App\Models\CheckInLog::where("event_id", $event_id)->where('checkout', "0000-00-00 00:00:00")->where("type_name", 'program')->where("attendee_id", $attendee_id)->where("type_id" , $id)->first();
        
        $checkInOutTime = date('Y-m-d H:i:s');
        
        if($checkin_status == "1") {

            if(!$log) {
                \App\Models\CheckInLog::create([
                    'event_id' => $event_id,
                    'organizer_id' => $organizer_id,
                    'attendee_id' => $attendee_id,
                    'checkin' => $checkInOutTime,
                    'delegate' => $delegate,
                    'type_name' => $filter_alias,
                    'type_id' => $id,
                ]);
            }

        } elseif ($checkin_status == "2") {

            if($log) {
                \App\Models\CheckInLog::where('id', $log->id)->update([
                    'checkout' => $checkInOutTime
                ]);
            }

        }

        return $checkInOutTime;
    }

    /**
     * @param mixed $formInput
     * 
     * @return [type]
     */
    public static function checkInOutStatus($formInput)
    {
        $result = \App\Models\CheckInLog::where('organizer_id', '=', $formInput['organizer_id'])->where('event_id', '=', $formInput['event_id'])->where('attendee_id', '=', $formInput['attendee_id'])->where('type_id', '=', $formInput['type_id'])->where('type_name', '=', $formInput['type_name'])->orderBy('id', 'desc')->first();

        if ($result) {

            if($result['checkout'] == '0000-00-00 00:00:00'){
                return 2;
            } else {
                return 1;
            }

        } else {
            return 1;
        }
    }
}
