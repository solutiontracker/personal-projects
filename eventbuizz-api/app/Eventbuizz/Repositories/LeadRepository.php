<?php

namespace App\Eventbuizz\Repositories;

use App\Models\EventEmailTemplate;
use \App\Mail\Email;
use Goodby\CSV\Export\Standard\Exporter;
use Goodby\CSV\Export\Standard\ExporterConfig;
use Illuminate\Support\Carbon;


class LeadRepository extends AbstractRepository
{
	public function __construct()
	{
		
	}
    
    /**
     * validateEvent 
     * (middleware potentially)
     * @param  mixed $event_id
     * @return array
     */
    function validateEvent($event_id)
    {
        $return = [
            'status' => 1,
            'message' => 'success'
        ];

        $status =\App\Models\Event::select("status")->where('id', $event_id)->whereNull('deleted_at')->pluck('status')->first();
        if (!is_null($status)) {
            if ((int) $status === 0 ) {
                $return['status'] = LEAD_EVENT_INACTIVE;
                $return['message'] = 'Provided event is not active';
            }
        } else {
            $return['status'] = LEAD_EVENT_NOT_FOUND;
            $return['message'] = 'Provided event is not found';
        }

        return $return;
    }    
        
    /**
     * getEventDetails
     *
     * @param  mixed $event_id
     * @return array
     */
    public function getEventDetails($formInput, $event_id)
    {
        $resArray = [];
        $event = \App\Models\Event::select([
            'id', 
            'organizer_name', 
            'organizer_id', 
            'name', 'url', 
            'start_date', 
            'end_date', 
            'start_time', 
            'end_time', 
            'status', 
            'timezone_id', 
            'language_id', 
            'country_id'
            ])->find($event_id)->toArray();
        
        $eventLogo = \App\Models\EventSetting::where("event_id", $event_id)->where("name", "header_logo")->pluck('value')->first();
        $event['eventLogo'] = $eventLogo ? $eventLogo : '';
        
        $eventInfo = \App\Models\EventInfo::select(['name', 'value'])->where("event_id", $event_id)->where(function($query){
            return $query->where('name', 'location_address')->orWhere('name', 'location_name');
        })->pluck('value', 'name');

        if($eventInfo){
            $eventInfo = $eventInfo->toArray();
            $event = array_merge($event, $eventInfo);    
        }

        $eventCountry = \App\Models\Country::select('name')->where('id', '=', $event['country_id'])->first();
        $event['location_country'] = $eventCountry->name;

        $resArray["event"] = $event;

        if($formInput['eventSettings'])
        {
            $event_settings = $this->getEventSettings($event_id);
            $resArray['eventSettings'] = $event_settings['status'] ? $event_settings['data']['eventSettings'] : (object) [];
        }
        if($formInput['leadSettings'])
        {
           $leadSettings = $this->getLeadSettings($event_id);
           $resArray['leadSettings'] = $leadSettings['status'] ? $leadSettings['data']['leadSettings'] : (object) [];
        }
        
        return ["status" => 1, "data" => $resArray];
    }
       
    /**
     * getLeadSettings
     *
     * @param  mixed $event_id
     * @return array
     */
    public function getLeadSettings($event_id)
    {
        $leadSettings = \App\Models\LeadSetting::where("event_id", $event_id)->first();
        if(is_null($leadSettings)){
            return [
                "status" => 0,
                "response_type" => LEAD_SETTINGS_NOT_FOUND,
                "message" => "Lead Settings not found"
            ];
        }
        $moduleStatus = \App\Models\EventModuleOrder::select(['status'])->where('event_id', $event_id)->where('alias', 'leadsmanagment')->first();
        $leadSettings['mode'] =  $leadSettings['lead_user_without_contact_person'] === 1 && $moduleStatus->status === 1 ? "lead_user" : "contact_person" ;
        unset($leadSettings['lead_user_without_contact_person']);
        $leadTerms = \App\Models\LeadTerm::where('event_id', $event_id)->first();
        $leadSettings['terms_conditons'] = $leadTerms ? $leadTerms->term_text : '';
        array_walk_recursive($leadSettings, function (&$item, $key) {
            $item = $item === null ? '' : $item;
        });
        return [
            "status" => 1,
            "data" => ["leadSettings" => $leadSettings]
        ];

    }
    
    /**
     * getEventSettings
     *
     * @param  mixed $event_id
     * @return array
     */
    public function getEventSettings($event_id)
    {
        $eventSettings = \App\Models\EventSetting::select(['name', 'value'])->where("event_id", $event_id)->pluck('value', 'name')->toArray();
        if(is_null($eventSettings)){
            return [
                "status" => 0,
                "response_type" => LEAD_EVENT_SETTINGS_NOT_FOUND,
                "message" => "Lead Settings not found"
            ];
        }
        array_walk_recursive($eventSettings, function (&$item, $key) {
            $item = $item === null ? '' : $item;
        });
        return [
            "status" => 1,
            "data" => ["eventSettings" => $eventSettings,]
        ];
    }

    /**
     * @param $formInput
     * @return array
     */
    public function getContactPersonSponsorsExhibitors($formInput)
    {
        if($formInput['mode'] !== "contact_person")
        {
            return [
                "status" => 0,
                "response_type" => INVALID_USER_MODE,
                "message" => "Server setting has been changed please export your leads records and logout",
            ];
        }
        $auth_user_id = $formInput->user()->id;
        $sponsors = \App\Models\EventSponsor::select(['id', 'name','logo', 'booth'])
        ->where('event_id', $formInput['event_id'])->whereHas('contactPersons', function($q) use($auth_user_id) { 
            $q->where('attendee_id', $auth_user_id);
            $q->whereNull('conf_event_sponsor_attendees.deleted_at');
        })->whereNull('deleted_at')->get();
        $exhibitors = \App\Models\EventExhibitor::select(['id', 'name','logo', 'booth'])
        ->where('event_id', $formInput['event_id'])->whereHas('contactPersons', function($q) use($auth_user_id) {
             $q->where('attendee_id', $auth_user_id); 
             $q->whereNull('conf_event_exhibitor_attendees.deleted_at'); 
        })->whereNull('deleted_at')->get();
        
        if(is_null($sponsors) && is_null($exhibitors)){
            return [
                "status" => 0,
                "response_type" => USER_NOT_ATTACHED_TO_SPONSOR_OR_EXHIBITOR,
                "message" => "User is not attached to any sponsor or exhibitor",
            ];
        }

        return [
            "status" => 1,
            "data" => [
                'sponsors' => $sponsors,
                'exhibitor' => $exhibitors
            ]
        ];
    }

    /**
     * @param $formInput
     * @return array
     */
    public function getScannedLeadAttendeeInfo($formInput)
    {
        if(!isset($formInput['url_id'])){
            return  [
                "status" => 0,
                "response_type" => SHORT_URL_ID_NOT_PROVIDED,
                "message" => "Short url id not provided",
            ];
        }
        
        $longUrl = \App\Models\URLShortner::select(['attendee_id', 'event_id']);
        
        if(is_numeric($formInput['url_id'])){
            $longUrl = $longUrl->where('id', $formInput['url_id']);
        }else{
            $longUrl = $longUrl->where('uuid', $formInput['url_id']);
        }
        
        $longUrl = $longUrl->first();
        
        if(!$longUrl){
            return  [
                "status" => 0,
                "response_type" => INVALID_SHORT_URL_ID,
                "message" => "Invalid Short url id",
            ];
        }
        
       $gdpr = \App\Models\EventGdprSetting::select(['enable_gdpr'])->where('event_id', $longUrl['event_id'])->pluck('enable_gdpr')->first();
       $event_language_id = \App\Models\Event::select('language_id')->where('id', $longUrl['event_id'])->pluck('language_id')->first();


        $attendee = \App\Models\Attendee::select(['first_name', 'last_name', 'email', 'image', 'phone'])
        ->where('id', $longUrl['attendee_id'])
        ->first();

        
        if(is_null($attendee)){
            return  [
                "status" => 0,
                "response_type" => ATTENDEE_NOT_FOUND,
                "message" => "Attendee Not Found",
            ];
        } 

        $attendee = $attendee->toArray();

        $currentEventAttendee = \App\Models\EventAttendee::where('attendee_id', $longUrl['attendee_id'])->where('event_id', $longUrl['event_id'])->first();

        if((int)$gdpr === 1)
       {
           if($currentEventAttendee && $currentEventAttendee["gdpr"] !== 1)
           {
                return  [
                    "status" => 0,
                    "response_type" => ATTENDEE_GDPR_DISABLED,
                    "message" => "This attendee have not accepted the GDPR and can therefor not be scanned. The GDPR setting can be changed in Edit profile",
                ];
           }
       }

       $phoneArr = explode("-",$attendee['phone']);
       $attendee['phone_code'] = $phoneArr[0];
       $attendee['phone_number'] = $phoneArr[1];
       unset($attendee['phone']);

       array_walk_recursive($attendee, function (&$item, $key) {
           $item = $item === null ? '' : $item;
        });
        
        $attendeeInfo = \App\Models\AttendeeInfo::select(['name', 'value'])
        ->where("attendee_id", $longUrl['attendee_id'])
        ->where("languages_id", $event_language_id)
        ->pluck('value', 'name')->toArray();

        $attendeePrivateCountry = \App\Models\Country::select('name')->where('id', '=', $attendeeInfo['private_country'])->first();
        $attendee['private_country'] = $attendeePrivateCountry->name;

        array_walk_recursive($attendeeInfo, function (&$item, $key) {
            $item = $item === null ? '' : $item;
        });

        $InfoValues =[
            'company_name',
            'private_house_number',
            'private_street',
            'private_post_code',
            'private_city',
            'title',
        ];

        foreach ($attendeeInfo as $key => $value) {
            if(in_array($key, $InfoValues, true)){
                    $attendee[$key] = $value;
            }
        }

        $attendeeOrder = \App\Models\BillingOrder::select('id')->where('attendee_id', $longUrl['attendee_id'])
            ->where('event_id', $longUrl['event_id'])
            ->first();

        if($attendeeOrder){
            // company_detail
            $attendeeBillingDetails = \App\Models\AttendeeBilling::select([
                                        'billing_company_house_number',
                                        'billing_company_street',
                                        'billing_company_post_code',
                                        'billing_company_city',
                                        'billing_company_country',
                                        ])->where('order_id', $attendeeOrder->id)
                                        ->first();
                                        
                if($attendeeBillingDetails){
                    $attendeeBillingDetails = $attendeeBillingDetails->toArray();
                    array_walk_recursive($attendeeBillingDetails, function (&$item, $key) {
                        $item = $item === null ? '' : $item;
                    });
                    $attendeeCompanyCountry = \App\Models\Country::select('name')->where('id', '=', $attendeeBillingDetails['billing_company_country'])->first();
                foreach ($attendeeBillingDetails as $key => $value) {
                    if($key === 'billing_company_country'){
                            $attendee['billing_company_country'] = $attendeeCompanyCountry->name;
                    } else{
                        $attendee[$key] = $value;
                    }       
                }
            }
        }


       
        return [
            "status" => 1,
            "data" => [
                "attendee" => $attendee,
            ]
        ];
    }

    /**
     * @param $formInput
     * @return array
     */
    public function getContactPersonProfileData($formInput)
    {
        if($formInput['mode'] !== "contact_person")
        {
            return [
                "status" => 0,
                "response_type" => INVALID_USER_MODE,
                "message" => "Server setting has been changed please export your leads records and logout",
            ];
        }
        if(!isset($formInput['contact_person_profile'])){
            return [
                "status" => 0,
                "response_type" => CONTANT_PERSON_PROFILE_NOT_PROVIDED,
                "message" => "Contact Person Profile Not Provided",
            ];
        }
        if(!isset($formInput['profile_id'])){
            return [
                "status" => 0,
                "response_type" => PROFILE_ID_NOT_PROVIDED,
                "message" => "Profile ID Not Provided",
            ];
        }
        else if($formInput['contact_person_profile'] !== "sponsor" && $formInput['contact_person_profile'] !== "exhibitor"){
            return [
                "status" => 0,
                "response_type" => INVALID_CONTANT_PERSON_PROFILE,
                "message" => "Invalid Contact Person Type",
            ];
        }

        if($formInput['contact_person_profile'] === "sponsor")
        {
            return $this->getSponsorProfileDetails($formInput['event_id'], $formInput['profile_id']);
        }
            return $this->getExhibitorProfileDetails($formInput['event_id'], $formInput['profile_id']);
    }
    
    /**
     * getSponsorProfileDetails
     *
     * @param  mixed $formInput
     * @return void
     */
    public function getSponsorProfileDetails($event_id, $sponsor_id)
    {   
        $response = [];
        // settings
        $sponsorLeadSettings = $this->getSponsorSettings($event_id);
        $response['settings'] = $sponsorLeadSettings;
        // product-catalogue 
        if($sponsorLeadSettings['catalogue_product'] === 1){         
            $sponsorProducts = $this->getProfileProducts($event_id, 'sponsor', $sponsor_id);
            $response['products'] = $sponsorProducts;
        }
        // consent-managements 
        if($sponsorLeadSettings['consent_management'] === 1){
            $sponsorSignUps = $this->getProfileSignUps($event_id, 'sponsor', $sponsor_id);
            $response['signups'] = $sponsorSignUps;
        }
        // survey
        if($sponsorLeadSettings['attendees_surveys'] === 1){
            $sponsorSurvey = $this->getProfileSurveys($event_id, 'sponsor', $sponsor_id);
            $response['survey'] = $sponsorSurvey;
        }
        return [
            "status" => 1, 
            "data" => $response
        ];
    } 
       
    /**
     * getExhibitorProfileDetails
     *
     * @param  mixed $formInput
     * @return void
     */
    public function getExhibitorProfileDetails($event_id, $exhibitor_id)
    {
        $response = [];
        // settings
        $exhibitorLeadSettings = $this->getExhibitorSettings($event_id);
        $response['settings'] = $exhibitorLeadSettings;
        // product-catalogue 
        if($exhibitorLeadSettings['catalogue_product'] === 1){         
            $exhibitorProducts = $this->getProfileProducts($event_id, 'exhibitor' ,  $exhibitor_id);
            $response['products'] = $exhibitorProducts;
        }
        // consent-management
        if($exhibitorLeadSettings['consent_management'] === 1){
            $exhibitorSignUps = $this->getProfileSignUps($event_id, 'exhibitor', $exhibitor_id);
            $response['signups'] = $exhibitorSignUps;
        }
        if($exhibitorLeadSettings['attendees_surveys'] === 1){
            $exhibitorSurvey = $this->getProfileSurveys($event_id, 'exhibitor', $exhibitor_id);
            $response['survey'] = $exhibitorSurvey;
        }
        return [
            "status" => 1, 
            "data" => $response
        ];
    }
    
    /**
     * getSponsorSettings
     *
     * @param  mixed $formInput
     * @return void
     */
    public function getSponsorSettings($event_id)
    {
        $sponsor_settings = \App\Models\SponsorSetting::select([

            "catalogue_product",
            "consent_management",
            "bcc_emails",
        ])->where('event_id', $event_id)->get()->toArray();

        $lead_settings = \App\Models\LeadSetting::select([
            "recieve_lead_email_on_save",
            "show_lead_email_button",
            "enable_signature",
            "enable_auto_capture",
            "attendees_surveys",
        ])->where('event_id', $event_id)->get()->toArray();

        $settings = array_merge($sponsor_settings[0],$lead_settings[0]);
        
        array_walk_recursive($settings, function (&$item, $key) {
            $item = $item === null ? '' : $item;
        });
        return $settings;
    }
        
    /**
     * getExhibitorSettings
     *
     * @param  mixed $event_id
     * @return void
     */
    public function getExhibitorSettings($event_id)
    {
        $exhibitor_settings = \App\Models\ExhibitorSetting::select([
            "catalogue_product",
            "consent_management",
            "bcc_emails",
        ])->where('event_id', $event_id)->get()->toArray();

         $lead_settings = \App\Models\LeadSetting::select([
            "recieve_lead_email_on_save",
            "show_lead_email_button",
            "enable_signature",
            "enable_auto_capture",
            "attendees_surveys",
        ])->where('event_id', $event_id)->get()->toArray();

        $settings = array_merge($exhibitor_settings[0],$lead_settings[0]);

        array_walk_recursive($settings, function (&$item, $key) {
            $item = $item === null ? '' : $item;
        });
        return $settings;
    }
    
        
    /**
     * getProfileProducts
     *
     * @param  mixed $event_id
     * @param  mixed $profile_type
     * @param  mixed $profile_id
     * @return void
     */
    public function getProfileProducts($event_id, $profile_type, $profile_id)
    {
        $products = \App\Models\CatalogueProduct::where('event_id', $event_id)->where('type', $profile_type)->where('type_id', $profile_id)->get()->toArray();
        array_walk_recursive($products, function (&$item, $key) {
            $item = $item === null ? '' : $item;
        });
        return $products;
    }
    
      
    /**
     * getProfileSignUps
     *
     * @param  mixed $event_id
     * @param  mixed $profile_type
     * @param  mixed $profile_id
     * @return void
     */
    public function getProfileSignUps($event_id, $profile_type, $profile_id)
    {
        $signups = \App\Models\ConsentManagement::where('event_id', $event_id)->where('type', $profile_type)->where('type_id', $profile_id)->get()->toArray();
        array_walk_recursive($signups, function (&$item, $key) {
            $item = $item === null ? '' : $item;
        });
        return $signups;
    }
          
    /**
     * getProfileSurveys
     *
     * @param  mixed $event_id
     * @param  mixed $profile_type
     * @param  mixed $profile_id
     * @return void
     */
    public function getProfileSurveys($event_id, $profile_type, $profile_id)
    {
        $surveys = \App\Models\EventAttendeeSurvey::withTrashed()
        ->where('event_id','=',$event_id)
        ->where('user_type', $profile_type)
        ->where('user_id',$profile_id)
        ->with(['info', 'question'=>function($q) {
            return $q->withTrashed();
        }, 'question.info', 'question.answer'=>function($q) {
            return $q->withTrashed();
        },'question.answer.info','question.matrix'=>function($q){
            return $q->withTrashed();
        }])->get()->toArray();

        array_walk_recursive($surveys, function (&$item, $key) {
            $item = $item === null ? '' : $item;
        });

        return $surveys;
    }
    
    /**
     * syncLeadsFromDevice
     *
     * @param  mixed $formInput
     * @return void
     */
    public function syncLeadsFromDevice($formInput)
    {
        $event =  $formInput['event'];
        return  $this->syncLeadsFromDeviceAgainstAuthUser($formInput);
    }

    
    /**
     * syncLeadsFromDeviceAgainstAuthUser
     *
     * @param  mixed $formInput
     * @return void
     */
    public function syncLeadsFromDeviceAgainstAuthUser($formInput)
    {
        // user against profile type and event
        $auth_user_id = $formInput->user()->id;
        $os = $formInput['os'];
        $event_id = $formInput['event_id'];
        if($formInput['mode'] !== 'lead_user')
        {
            if(!isset($formInput['contact_person_profile'])){
                return [
                    "status" => 0,
                    "response_type" => CONTANT_PERSON_PROFILE_NOT_PROVIDED,
                    "message" => "Contact Person Profile Not Provided",
                ];
            }
            if(!isset($formInput['profile_id'])){
                return [
                    "status" => 0,
                    "response_type" => PROFILE_ID_NOT_PROVIDED,
                    "message" => "Profile ID Not Provided",
                ];
            }
            if($formInput['contact_person_profile'] !== "sponsor" && $formInput['contact_person_profile'] !== "exhibitor"){
                return [
                    "status" => 0,
                    "response_type" => INVALID_CONTANT_PERSON_PROFILE,
                    "message" => "Invalid Contact Person Type",
                ];
            }
            $validateContactPerson = $this->validateContactPerson($event_id, $formInput['contact_person_profile'], $formInput['profile_id'], $auth_user_id);
            if($validateContactPerson['status'] !== 1){
                return $validateContactPerson;
            }
        }
        else{
            
            $checkLeadModuleStatus = $this->leadsModuleStatus($formInput['event_id']);
            if($checkLeadModuleStatus['status'] !== 1 )
            {
                return $checkLeadModuleStatus;
            } 
            $validateLeadUser = $this->validateLeadUser($event_id, $auth_user_id);
            if($validateLeadUser['status'] !== 1){
                return $validateLeadUser;
            }

        }

        $newLeadWhereIn=[];

        foreach ($formInput['leads'] as $lead) {

            // check if deleted
            if($lead['deleted_at'] !== null && $lead['deleted_at'] !== "" && $lead['id'] !== null && $lead['id'] !== '')
            {
                $this->deleteLead($lead['id']);
                continue;
            }
            $leadObject = null;
            // check if updated else create new
            if($lead['id'] !== null && $lead['id'] !== ''){
                $leadObject = \App\Models\EventLead::where('id', $lead['id'])->where('contact_person_id');
                if($formInput['mode'] === "lead_user"){
                    $leadObject->where("contact_person_type", "")->where("type", 0);
                }
                $leadObject = $leadObject->first();
            } 
            if(is_null($leadObject)){
                if ($lead['device_id'] !== null && $lead['device_id'] !== '') {
                    $leadObject = \App\Models\EventLead::where('device_id', $lead['device_id']);
                    if($formInput['mode'] === "lead_user"){
                        $leadObject->where("contact_person_type", "")->where("type_id", 0);
                    }
                    $leadObject = $leadObject->first();
                }
            }
            if(is_null($leadObject)){
                $leadObject = new \App\Models\EventLead();
                $newLeadWhereIn[]=$lead['device_id'];
            }
            // save image
            if(isset($lead['image_file']) && $lead['image_file'] !== '' ){
                $leadObject->image_file = $this->saveImageFile($auth_user_id,$lead['image_file'], $os);
            }  else {
                $leadObject->image_file = $leadObject->image_file !== NULL ? $leadObject->image_file : '';
            }

            // save profile image data
            if (isset($lead['profile_image_data']) && $lead['profile_image_data'] != '') {
                $leadObject->profile_image_data = $this->saveImageFile($auth_user_id,$lead['profile_image_data'], $os);              
            } 
            else
            {
                $leadObject->profile_image_data = $leadObject->profile_image_data !== NULL ? $leadObject->profile_image_data : '';
            }
            
            // save lead
            $leadObject->device_id = $lead['device_id'];
            $leadObject->event_id = $event_id;
            $leadObject->contact_person_id = $auth_user_id;
            $leadObject->email = $lead['email'] ?? "";
            $leadObject->first_name = $lead['first_name'];
            $leadObject->last_name = $lead['last_name'] ?? "";
            $leadObject->rating = $lead['rating'] ?? "";
            $leadObject->initial = $lead['initial'] ?? "";
            $leadObject->notes = $lead['notes'] ?? "";
            $leadObject->permission_allowed = $lead['permission_allowed'] ?? "";
            $leadObject->term_text = $lead['term_text'] ?? "";
            $leadObject->lead_date = $lead['lead_date'] == '' ? date('Y-m-d H:i:s') : $lead['lead_date'];
            if($formInput['mode'] === 'contact_person')
            {
                $leadObject->type_id = $formInput['profile_id'];
                $leadObject->contact_person_type = $formInput['contact_person_profile'];
            }
            $leadObject->save();


            if(isset($lead['info']) && count($lead['info']) > 0){
                foreach ($lead['info'] as $key => $value) {
                  $info =  \App\Models\LeadInfo::where('name', $key)->where('lead_id', $leadObject->id)->where('languages_id', $formInput['event']['language_id'])->first();
                  if($info){
                      $info->value = $value;
                      $info->lead_id = $leadObject->id;
                      $info->device_id = $leadObject->device_id;
                      $info->languages_id = $formInput['event']['language_id'];
                      $info->save();
                  }
                  else{
                    \App\Models\LeadInfo::create([
                            "name" => $key,
                            "value" => $value,
                            "lead_id" => $leadObject->id,
                            "device_id" => $leadObject->device_id,
                            "languages_id" => $formInput['event']['language_id'],
                    ]);
                  }
                }
            }

            if($formInput['mode'] === "contact_person")
            {
                // save products, signups
                \App\Models\EventLeadProduct::where('lead_id', $leadObject->id)->delete();
                if($lead['product_ids']){
                    $old_products = $leadObject->catalogue_products_id;
                    $leadObject->catalogue_products_id = $lead['product_ids'];
                    $leadObject->save();

                    $product_ids = explode(',',$lead['product_ids']);
                    $old_product_ids = explode(',',$old_products);

                    $product_changed = ((count(array_diff($product_ids, $old_product_ids)) > 0) || (count(array_diff($old_product_ids, $product_ids)) > 0)) ? true : false;

                    $products = '';
                    foreach ($product_ids as $product_id){
                        \App\Models\EventLeadProduct::create([
                            "lead_id" => $leadObject->id,
                            "product_id" => $product_id
                        ]);
                        $product = \App\Models\CatalogueProduct::find($product_id);
                        $products .= '<p style="margin: 0 0 3px; padding:0;"><a href="'.config('app.eventcenter_url').'/_admin/downloadLeadsPromotions/'.$product->id.'">'.$product->product_name.'</a></p>';
                    }
                }

                // Consent
                    \App\Models\EventLeadConsent::where('lead_id', $leadObject->id)->delete();
                if($lead['signup_ids']){
                    $old_consents = $leadObject->consent_management_id;
                    $leadObject->consent_management_id = $lead['signup_ids'];
                    $leadObject->save();

                    $consent_ids = explode(',',$lead['signup_ids']);
                    $old_consent_ids = explode(',',$old_consents);

                    $consent_changed = ((count(array_diff($consent_ids, $old_consent_ids)) > 0) || (count(array_diff($old_consent_ids, $consent_ids)) > 0)) ? true : false;


                    $consents = '';
                    foreach ($consent_ids as $consent_id){
                        \App\Models\EventLeadConsent::create([
                            "lead_id" => $leadObject->id,
                            "consent_id" => $consent_id
                        ]);
                        $consent = \App\Models\ConsentManagement::find($consent_id);
                        $consents .= '<p>'.$consent->consent_name.'</p></br>';
                    }  
                }
                
                if(($lead['email'] && $lead['email'] !== "") && (($product_changed === true) || ($consent_changed === true))){
                    
                    $to_email = $lead['email'];
                    $contact_person = $formInput->user();
                    $contact_person_company = \App\Models\AttendeeInfo::where('attendee_id',$auth_user_id)->where('name','company_name')->where('languages_id',$formInput['event']['language_id'])->first();
                    $contact_person_company = $contact_person_company['value'] ? $contact_person_company['value'] : '';
                    $contact_person_title = \App\Models\AttendeeInfo::where('attendee_id',$auth_user_id)->where('name','title')->where('languages_id',$formInput['event']['language_id'])->first();
                    $contact_person_title = isset($contact_person_title['value']) ? $contact_person_title['value'] : '';
                    
                    $this->sendEmailLeadsPromotion(
                        $formInput['event'],
                        $to_email,
                        $lead,
                        $contact_person,
                        $contact_person_company,
                        $contact_person_title,
                        $products,
                        $consents, 
                        $formInput['contact_person_profile'],
                        $formInput['profile_id']
                    );
                }
                
                // save survey
                if($lead['lead_survey'] !== '') {
                    // Delete Old results
                    \App\Models\EventAttendeeSurveyResult::where('device_id', $lead['device_id'])->delete();
                    // Delete Old results
                    \App\Models\EventHubSurveyAttendeeResult::where('device_id', $lead['device_id'])->delete();

                    foreach($lead['lead_survey'] as $lead_survey) {
                        $lead_result = [];
                        $lead_result['id'] = $lead_survey['id'];
                        $lead_result['survey_id'] = $lead_survey['survey_id'];
                        $lead_result['answer'] = $lead_survey['answer'];
                        $lead_result['answer_id'] = $lead_survey['answer_id'];
                        $lead_result['attendee_id'] = 0;
                        $lead_result['comment'] = $lead_survey['comment'];
                        $lead_result['event_id'] = $lead_survey['event_id'];
                        $lead_result['question_id'] = $lead_survey['question_id'];
                        $lead_result['updated_at'] = $lead_survey['updated_at'];
                        $lead_result['is_updated'] = $lead_survey['is_updated'];
                        $lead_result['lead_id'] = $leadObject['id'];
                        $lead_result['device_id'] =$lead['device_id'];
                        $this->saveAttendeeSurveyResults($lead_result);
                    }
                }

            }
        }

        if(count($newLeadWhereIn) > 0){
                $this->createCsvAndSendEmail($formInput['profile_id'], $formInput['contact_person_profile'], $newLeadWhereIn, $formInput['event_id'], $auth_user_id, $formInput['mode']);
        }

        $leads = $this->getLeads($formInput);
        return [
            "status" => 1,
            "data" => [
                "message" => "Data synced successfully.",
                "leads" => $leads['data']['leads'],
                "timestamp" => $leads['data']['timestamp']
            ]
        ];

    }
    
    /**
     * validateContactPerson
     *
     * @param  mixed $event_id
     * @param  mixed $profile_type
     * @param  mixed $profile_id
     * @param  mixed $auth_user_id
     * @return void
     */
    public function validateContactPerson($event_id, $profile_type, $profile_id, $auth_user_id)
    {
        $profileModel = $profile_type === 'sponsor' ? "\App\Models\EventSponsor" : "\App\Models\EventExhibitor";
        $profileAttendeeModel = $profile_type === 'sponsor' ? "\App\Models\EventSponsorAttendee" : "\App\Models\EventExhibitorAttendee";
        $profile = $profileModel::find($profile_id);
            if($profile->event_id == $event_id) {
                $profile_contact_person = $profileAttendeeModel::where($profile_type.'_id', $profile_id)->where('attendee_id' ,$auth_user_id)->first();
                if($profile_contact_person) {
                    return [
                        "status"=> 1,
                        "message"=> 'success' 
                    ];
                }
                    return [
                        "status" => 0,
                        "response_type"=> CONTACT_PERSON_NOT_ASSIGNED_TO_PROFILE_TYPE,
                        "message"=> 'Current User is not Assigned to this '. $profile_type 
                    ];
            }
             return   [
                "status"=> PROFILE_NOT_FOUND_AGAINST_EVENT,
                "message"=> 'The given '. $profile_type . ' is not does not exist against the given Event'
            ];
    }
    
    /**
     * deleteLead
     *
     * @param  mixed $lead_id
     * @return void
     */
    public function deleteLead($lead_id)
    {
        $lead = \App\Models\EventLead::find($lead_id);
        if($lead){
            $lead->delete();
        }
        return true;
    }
    
    /**
     * saveImageFile
     *
     * @param  mixed $contact_person_id
     * @param  mixed $image_file
     * @param  mixed $os
     * @return void
     */
    public function saveImageFile($contact_person_id, $image_file, $os)
    {
        $image_file_name = $contact_person_id . '_' . uniqid() . '.jpg';
        $string_base = $image_file;
        if ($os == 'android') {
            file_put_contents(dirname(public_path(), 2).'/public/assets/leads/' . $image_file_name, base64_decode(urldecode($string_base)));
        } else {
            file_put_contents(dirname(public_path(), 2).'/public/assets/leads/' . $image_file_name, base64_decode($string_base));
        }
        return $image_file_name;
    }
        
    /**
     * saveAttendeeSurveyResults
     *
     * @param  mixed $surveyData
     * @return void
     */
    public function saveAttendeeSurveyResults($surveyData)
    {
        $event_id = $surveyData['event_id'];
        $attendee_id 	= $surveyData['attendee_id'];
        $answer = $surveyData['answer'];
        $comment = $surveyData['comment'];
        $question_id = $surveyData['question_id'];
        $answer_id = $surveyData['answer_id'];
        $survey_id = $surveyData['survey_id'];
        $lead_id = $surveyData['lead_id'];
        $device_id = $surveyData['device_id'];
        $questionDetails = \App\Models\EventAttendeeSurveyQuestion::find($question_id);
        $lead_id_for_is_anonymous = $lead_id;
        $device_id_is_anonymous = $device_id;
        if((int)$questionDetails->is_anonymous === 1){
            $lead_id_for_is_anonymous = 0;
            $device_id_is_anonymous = null;
        }
        $check_if_answered = false;
        if($questionDetails->question_type !== 'matrix'){
            $check_if_answered = \App\Models\EventAttendeeSurveyResult::where('event_id', $event_id)
            ->where('attendee_id', $attendee_id)
            ->where('question_id', $question_id)
            ->where('answer_id', $answer_id)
            ->where('lead_id', $lead_id)
            ->where('device_id', $device_id)
            ->whereNull('deleted_at')
            ->first();
        }

        if(!$check_if_answered)
        {
            \App\Models\EventHubSurveyAttendeeResult::create([
                "event_id"=>  $event_id,
                "survey_id"=>  $survey_id,
                "question_id"=>  $question_id,
                "attendee_id"=>  $attendee_id,
                "lead_id"=>  $lead_id,
                "device_id"=>  $device_id,
            ]);

            \App\Models\EventAttendeeSurveyResult::create([
                "event_id"=> $event_id,
                "survey_id"=>  $survey_id,
                "question_id"=>  $question_id,
                "answer_id"=>  $answer_id ?? "",
                "answer"=> $answer ?? "",
                "comments"=>  $comment ?? "",
                "attendee_id"=>  $attendee_id,
                "lead_id"=>  $lead_id_for_is_anonymous,
                "device_id"=>  $device_id_is_anonymous,
            ]);
        }
    }
    
    /**
     * sendEmailLeadsPromotion
     *
     * @param  mixed $event
     * @param  mixed $to_email
     * @param  mixed $lead
     * @param  mixed $contact_person
     * @param  mixed $contact_person_company
     * @param  mixed $contact_person_title
     * @param  mixed $products
     * @param  mixed $consents
     * @param  mixed $contact_person_profile
     * @param  mixed $profile_id
     * @return void
     */
    public function sendEmailLeadsPromotion($event,$to_email,$lead,$contact_person,$contact_person_company,$contact_person_title,$products,$consents, $contact_person_profile, $profile_id)
    {
        $template = \App\Models\LeadHubTemplate::where('alias', 'lead_user_promotions')->where('type', $contact_person_profile)->where('type_id', $profile_id)->first();
        $body = $template->template;
        info([$body, 'hub']);
        $subject = $template->subject;
        if(!$template){
            $language_id = $event['language_id'];
            $template = EventEmailTemplate::where('event_id', '=', $event['id'])
                ->where('alias','lead_user_promotions')->where('type','email')->with(['info' => function ($q)use($language_id) {
                    $q->where('languages_id', '=', $language_id);
                }])->get()->toArray();
            foreach ($template[0]['info'] as $info) {
                if ($info['name'] == 'template') {
                    $body = $info['value'];
                }
                if ($info['name'] == 'subject') {
                    $subject = $info['value'];
                }
            }
        }


        if(!$template){
            $language_id = $event['language_id'];
            $template = EventEmailTemplate::where('event_id', '=', $event['id'])
                ->where('alias','leads_email')->where('type','email')->with(['info' => function ($q)use($language_id) {
                    $q->where('languages_id', '=', $language_id);
                }])->get()->toArray();

            foreach ($template[0]['info'] as $info) {
                if ($info['name'] == 'template') {
                    $body = $info['value'];
                }
                if ($info['name'] == 'subject') {
                    $subject = $info['value'];
                }
            }
        }
        $body = stripslashes($body);

        $contact_person['info'] = [
            "title" => $contact_person_title,
            "company_name" => $contact_person_company,
        ];
        $contact_person['consents'] = $consents;
        $contact_person['products'] = $products;

        $body = $this->emailReplaceTags($event, 'contact_person', $contact_person_profile, $profile_id, $contact_person, $body);

        $body = str_replace("{lead_user_first_name}", $lead['name'], $body);
        $body = str_replace("{lead_user_last_name}", '', $body);
        $body = str_replace("{contact_person_first_name}", $lead['name'], $body);
        $body = str_replace("{contact_person_last_name}", '', $body);

        $data = array();
		$data['template'] = 'lead_user_promotions';
		$data['event_id'] = $event['id'];
		$data['subject'] = $subject;
		$data['content'] = $body;
		$data['view'] = 'email.plain-text';
		$data['from_name'] = $event['organizer_name'];
		$data['email'] = $to_email;
		\Mail::to($to_email)->send(new Email($data));

        return true;
    }
    
    /**
     * getLeads
     *
     * @param  mixed $formInput
     * @return void
     */
    public function getLeads($formInput)
    {
        $auth_user_id = $formInput->user()->id;

        if($formInput['mode'] !== "lead_user") {
            if(!isset($formInput['contact_person_profile'])) {
                return [
                    "status" => 0,
                    "response_type" => CONTANT_PERSON_PROFILE_NOT_PROVIDED,
                    "message" => "Contact Person Profile Not Provided",
                ];
            }
            if(!isset($formInput['profile_id'])) {
                return [
                    "status" => 0,
                    "response_type" => PROFILE_ID_NOT_PROVIDED,
                    "message" => "Profile ID Not Provided",
                ];
            }
            if($formInput['contact_person_profile'] !== "sponsor" && $formInput['contact_person_profile'] !== "exhibitor"){
                return [
                    "status" => 0,
                    "response_type" => INVALID_CONTANT_PERSON_PROFILE,
                    "message" => "Invalid Contact Person Type",
                ];
            }

            $validateContactPerson = $this->validateContactPerson($formInput['event_id'], $formInput['contact_person_profile'], $formInput['profile_id'], $auth_user_id);
            
            if($validateContactPerson['status'] !== 1 )
            {
                return $validateContactPerson;
            }

        }
        else 
        {
            $checkLeadModuleStatus = $this->leadsModuleStatus($formInput['event_id']);
            if($checkLeadModuleStatus['status'] !== 1 )
            {
                return $checkLeadModuleStatus;
            } 
            $validateLeadUser = $this->validateLeadUser($formInput['event_id'], $auth_user_id);
            if($validateLeadUser['status'] !== 1 )
            {
                return $validateLeadUser;
            } 
        }

        $leads = \App\Models\EventLead::select([
            "id",
            "event_id",
            "device_id",
            "contact_person_id",
            "type_id",
            "contact_person_type",
            "email",
            "first_name",
            "last_name",
            "rating",
            "image_file",
            "initial",
            "permission_allowed",
            "lead_date",
            "notes",
            "created_at",
            "updated_at",
            "deleted_at",
            "term_text",
            "profile_image_data",
        ])->withTrashed()->where('contact_person_id', $auth_user_id);

        if($formInput['mode'] !== "lead_user"){
            $leads = $leads->where('contact_person_type', $formInput['contact_person_profile'])
            ->where('type_id', $formInput['profile_id']);
        } else {
            $leads = $leads->where('contact_person_type', "")
            ->where('type_id', 0);
        }

        $withArray = [
            'info' => function($q) use ($formInput){ $q->select([
                        "id",
                        "name",
                        "value",
                        "lead_id",
                        "device_id",
            ])->where('languages_id', $formInput['event']['language_id'])->whereNull('deleted_at');}
        ];

        if($formInput['mode'] !== "lead_user")
        {
            $leadSettings = $formInput['contact_person_profile'] === "sponsor" ? $this->getSponsorSettings($formInput['event_id']) : $this->getExhibitorSettings($formInput['event_id']);
            if($leadSettings['catalogue_product'] === 1){
                $withArray['products'] = function($q){ $q->whereNull("conf_event_lead_product.deleted_at");};
            }
            if($leadSettings['consent_management'] === 1){
                $withArray['signups'] = function($q){ $q->whereNull("conf_event_lead_consent.deleted_at");};
            }
            if($leadSettings['attendees_surveys'] === 1){
                $withArray[] = 'surveyResults';
            }
            
        }

        if($formInput['timestamp']){
            $leads = $leads->where("updated_at", ">", $formInput['timestamp']);
        }

        $leads = $leads->with($withArray)->orderBy('first_name')->get()->toArray();

        if ($formInput['mode'] !== "lead_user") {
            foreach ($leads as $key => $lead) {
                $signup_ids = [];
                foreach ($lead['signups'] as $signup) {
                    $signup_ids[] = $signup['id'];
                }
                $leads[$key]['signups'] = implode(',', $signup_ids);

                $product_ids = [];
                foreach ($lead['products'] as $product) {
                    $product_ids[] = $product['id'];
                }
                $leads[$key]['products'] = implode(',', $product_ids);
            }
        }
        
        array_walk_recursive($leads, function (&$item, $key) {
            $item = $item === null ? '' : $item;
        });

        return  [
            "status" => 1,
            "data" => [
                "leads" => $leads,
                "timestamp" => date('Y-m-d H:i:s'),
            ]
        ];

    }

    /**
     * validateLeadUser
     *
     * @param  mixed $email
     * @param  mixed $event_id
     * @return void
     */
    public function validateLeadUser($event_id, $id)
    {
        $return = [
            'status' => 1,
            'message' => 'success'
        ];

        $lead_user = \App\Models\LeadUser::where('id',$id)->where('event_id', $event_id)->whereNull('deleted_at')->first();
        $leadSettings = \App\Models\LeadSetting::where("event_id", $event_id)->first();
        if (is_null($lead_user)){

            $return['status'] = 0;
            $return['response_type'] = LEAD_USER_NOT_FOUND;
            $return['message'] = 'Please provide a valid email';
        }
        elseif ($leadSettings->enable_organizer_approval == 1 && $lead_user->approved !== 1) {
            $return['status'] = 0;
            $return['response_type'] = LEAD_USER_NOT_APPROVED;
            $return['message'] = 'You are not approved.';
        }
        else {
            if ($lead_user->status === 0) {
                $return['status'] = 0;
                $return['response_type'] = LEAD_USER_INACTIVE;
                $return['message'] = 'You account is deactivated to login.';
            }
        }

        return $return;
    }
 
    public function leadsModuleStatus($event_id)
    {
        $status = \App\Models\EventModuleOrder::select(['status'])->where('event_id', $event_id)->where('alias', 'leadsmanagment')->first();
        if (!$status->status) {
            return [
                'status' => 0,
                'response_type' => INACTIVE_LEADS_MODULE,
                'message' => 'Leads module is inactive'
            ];
        }
        return [
            'status' => 1,
            'message' => 'success'
        ];
    }


    
    /**
     * createCsvAndSendEmail
     *
     * @param  mixed $profile_id
     * @param  mixed $profile_type
     * @param  mixed $newLeadWhereIn
     * @param  mixed $event_id
     * @param  mixed $auth_user_id
     * @param  mixed $mode
     * @return void
     */
    public function createCsvAndSendEmail($profile_id, $profile_type, $newLeadWhereIn = [], $event_id, $auth_user_id, $mode)
    {

        $event =  \App\Models\Event::where('id', '=', $event_id)->WhereNull('deleted_at')->first()->toArray();

        if ($mode === 'contact_person') {
            if ($profile_type == 'sponsor') {
                $setting = $this->getSponsorSettings($event_id);
            } else {
                $setting = $this->getExhibitorSettings($event_id);
            }
        } else {
            $setting = $this->getLeadSettings($event)['data']['leadSettings'];
        }


        if ($setting['recieve_lead_email_on_save']) {

            if ($mode === 'contact_person') {
                $list = $this->getExportProfileDataSet($profile_id, $profile_type, $newLeadWhereIn, $event, $auth_user_id, $mode);
            } else {
                $list = $this->getExportLeadUserDataSet($event, $auth_user_id, $mode, $newLeadWhereIn);
            }

            $filename = time() . '_' . rand() . '.csv';

            $this->export($event['id'], $list, $filename, storage_path('app/lead/csv/'), ';', true);

            if (!is_null($setting['bcc_emails']) and $setting['bcc_emails'] != '') {
                $bcc_emails = explode(',', $setting['bcc_emails']);
            } else {
                $bcc_emails = [];
            }

            if($mode === 'contact_person'){
                $contact_person_detail = \App\Models\Attendee::where('id', $auth_user_id)->first();
                $info = \App\Models\AttendeeInfo::where('attendee_id', $auth_user_id)->where('languages_id', $event['language_id'])->whereIn('name', ['company_name', 'title'])->get();
                $contact_person_detail['info'] = readArrayKey(["info"=>$info], [], 'info');
            }else{
                $contact_person_detail = \App\Models\LeadUser::where('id', $auth_user_id)->first();
            }

            $templateData = $this->leadSavedEmailTemplate($event_id, $profile_id, $profile_type, $contact_person_detail, $mode);

            $to_email = (isset($contact_person_detail['email'])) ? $contact_person_detail['email'] : '';

            $data['email'] = $to_email;
            $data['from_name'] = $event['organizer_name'];
            $data['organizer_id'] = $event['organizer_id'];
            $data['attachment'] = [['path' => storage_path('app/lead/csv/' . $filename), 'name' => 'NewLeads.csv']];
            $data['event_id'] = $event['id'];
            $data['bcc'] = $bcc_emails;
            $data['subject'] = $templateData['subject'];
            $data['content'] = $templateData['contents'];
            $data['view'] = 'email.plain-text';
            $data['from_name'] = $event['organizer_name'];
            \Mail::to($to_email)->send(new Email($data));

            unlink(storage_path('app/lead/csv/' . $filename));
        }
    }



    
    /**
     * getExportLeadUserDataSet
     *
     * @param  mixed $event
     * @param  mixed $auth_user_id
     * @param  mixed $mode
     * @param  mixed $newLeadWhereIn
     * @return void
     */
    function getExportLeadUserDataSet($event, $auth_user_id,  $mode, $newLeadWhereIn = [])
    {

        $leads = \App\Models\EventLead::select([
            "id",
            "event_id",
            "device_id",
            "contact_person_id",
            "type_id",
            "contact_person_type",
            "email",
            "first_name",
            "last_name",
            "rating",
            "image_file",
            "initial",
            "permission_allowed",
            "lead_date",
            "notes",
            "created_at",
            "updated_at",
            "deleted_at",
            "term_text",
            "profile_image_data",
        ])->withTrashed()->where('contact_person_id', $auth_user_id)
            ->where('type_id', '0')->whereIn('device_id', $newLeadWhereIn)->with([
                'info' => function ($q) use ($event) {
                    $q->select([
                        "id",
                        "name",
                        "value",
                        "lead_id",
                        "device_id",
                    ])->where('languages_id', $event['language_id'])->whereNull('deleted_at');
                }
            ])->orderBy('first_name')->get()->toArray();

        $lead_user = \App\Models\LeadUser::where('id', $auth_user_id)->first();

        
        $export_list = array();
        $export_list[] = array(
            'Event Name',
            'Contact Person Name',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Company',
            'Title',
            'Rating',
            'Notes',
            'House #',
            'Street #',
            'Postal code',
            'City',
            'Country',
            'Company House #',
            'Company Street #',
            'Company Postal Code',
            'Company City',
            'Company Country',
            'Profile image',
            'Signatures',
        );


        foreach ($leads as $lead) {
            $lead_city = '';
            $lead_company_name = '';
            $lead_postal_code = '';
            $lead_street_number = '';
            $lead_job_title = '';
            $lead_country = '';
            $lead_phone = '';
            $lead_house_number = '';
            $lead_company_house_number = '';
            $lead_company_postal_code = '';
            $lead_company_street_number = '';
            $lead_company_city = '';
            $lead_company_country = '';


            foreach ($lead['info'] as $info) {
                if ($info['name'] == 'phone') {
                    $phone_number = $info['value'];
                    $phone_number = explode('-', $phone_number);
                    $phone_code = '';
                    $phone_number_actual = '';
                    if (trim($phone_number[0]) != '+' && trim($phone_number[0]) != '') {
                        $phone_code = $phone_number[0];
                    }
                    if (trim($phone_number[1]) != '') {
                        $phone_number_actual = $phone_number[1];
                    }
                    if ($phone_code != '') {
                        $phone_code = '\'' . $phone_code;
                        $lead_phone = $phone_code . '-' . $phone_number_actual;
                    } else {
                        $lead_phone = $phone_number_actual;
                    }
                }
                if ($info['name'] == 'company_name') {
                    $lead_company_name = $info['value'];
                }
                if ($info['name'] == 'job_title') {
                    $lead_job_title = $info['value'];
                }
                if ($info['name'] == 'postal_code') {
                    $lead_postal_code = $info['value'];
                }
                if ($info['name'] == 'street_number') {
                    $lead_street_number = $info['value'];
                }
                if ($info['name'] == 'city') {
                    $lead_city = $info['value'];
                }
                if ($info['name'] == 'country') {
                    $lead_country = $info['value'];
                }
                if ($info['name'] == 'house_number') {
                    $lead_house_number = $info['value'];
                }
                if ($info['name'] == 'company_postal_code') {
                    $lead_company_postal_code = $info['value'];
                }
                if ($info['name'] == 'company_street_number') {
                    $lead_company_street_number = $info['value'];
                }
                if ($info['name'] == 'company_city') {
                    $lead_company_city = $info['value'];
                }
                if ($info['name'] == 'company_country') {
                    $lead_company_country = $info['value'];
                }
                if ($info['name'] == 'company_house_number') {
                    $lead_company_house_number = $info['value'];
                }
            }
            $email = '';
            if ($lead['email']) {
                $email = $lead['email'];
            }
            $characters = array('¿' => 'ø', 'Œ' => 'å', '¾' => 'æ', '¯' => 'Ø', 'Ž' => 'é', '®' => 'Æ', 'â' => 'ä', 'Ÿ' => 'ü', '' => 'å', '' => 'é');

            foreach ($characters as $key => $char) {
                $event_name = str_replace($key, $char, $event['name']);
                $contact_person_f = str_replace($key, $char, $lead_user['name']);
                $first_name = str_replace($key, $char, $lead['first_name']);
                $last_name = str_replace($key, $char, $lead['last_name']);
                $email = str_replace($key, $char, $email);
                $lead_company_name = str_replace($key, $char, $lead_company_name);
                $lead_job_title = str_replace($key, $char, $lead_job_title);
                $lead_street_number = str_replace($key, $char, $lead_street_number);
                $lead_city = str_replace($key, $char, $lead_city);
                $lead_country = str_replace($key, $char, $lead_country);
                $lead_phone = str_replace($key, $char, $lead_phone);
                $lead_postal_code = str_replace($key, $char, $lead_postal_code);
                $lead_house_number = str_replace($key, $char, $lead_house_number);
                $lead_company_house_number = str_replace($key, $char, $lead_company_house_number);
                $lead_company_postal_code = str_replace($key, $char, $lead_company_postal_code);
                $lead_company_street_number = str_replace($key, $char, $lead_company_street_number);
                $lead_company_city = str_replace($key, $char, $lead_company_city);
                $lead_company_country = str_replace($key, $char, $lead_company_country);
            }


            $export_list[] = array(
                'Event Name' => $event_name,
                'Contact Person Name' => $contact_person_f,
                'First Name' => $first_name,
                'Last Name' => $last_name,
                'Email' => $email,
                'Phone' => $lead_phone,
                'Company' => $lead_company_name,
                'Title' => $lead_job_title,
                'Rating' => $lead['rating'],
                'Notes' => $lead['notes'],
                'House #' => $lead_house_number,
                'Street #' => $lead_street_number,
                'Postal code' => $lead_postal_code,
                'City' => $lead_city,
                'Country' => $lead_country,
                'Company House #' => $lead_company_house_number,
                'Company Street #' => $lead_company_street_number,
                'Company Postal code' => $lead_company_postal_code,
                'Company City' => $lead_company_city,
                'Company Country' => $lead_company_country,
                'Profile image' => $lead['profile_image_data'] !== '' ? config('app.eventcenter_url') .'/assets/leads/'.$lead['profile_image_data'] : '' ,
                'Signatures' => $lead['image_file'] !== '' ? config('app.eventcenter_url') .'/assets/leads/'.$lead['image_file'] : '',
            );
        }

        return $export_list;
    }

        
    /**
     * getExportProfileDataSet
     *
     * @param  mixed $profile_id
     * @param  mixed $profile_type
     * @param  mixed $newLeadWhereIn
     * @param  mixed $event
     * @param  mixed $auth_user_id
     * @param  mixed $mode
     * @return void
     */
    function getExportProfileDataSet($profile_id, $profile_type, $newLeadWhereIn = [], $event, $auth_user_id,  $mode)
    {

        $profileModel = $profile_type === 'sponsor' ? "\App\Models\EventSponsor" : "\App\Models\EventExhibitor";
        $profileKey = $profile_type === 'sponsor' ? "Sponsor Name" : "Exhibitor Name";

        $leads = \App\Models\EventLead::select([
            "id",
            "event_id",
            "device_id",
            "contact_person_id",
            "type_id",
            "contact_person_type",
            "email",
            "first_name",
            "last_name",
            "rating",
            "image_file",
            "initial",
            "permission_allowed",
            "lead_date",
            "notes",
            "created_at",
            "updated_at",
            "deleted_at",
            "term_text",
            "profile_image_data",
        ])->withTrashed()->where('contact_person_id', $auth_user_id)->where('contact_person_type', $profile_type)
            ->where('type_id', $profile_id)->whereIn('device_id', $newLeadWhereIn)->with([
                'info' => function ($q) use ($event) {
                    $q->select([
                        "id",
                        "name",
                        "value",
                        "lead_id",
                        "device_id",
                    ])->where('languages_id', $event['language_id'])->whereNull('deleted_at');
                }, 'products' => function ($q) {
                    $q->whereNull("conf_event_lead_product.deleted_at");
                },
                'signups' => function ($q) {
                    $q->whereNull("conf_event_lead_consent.deleted_at");
                }
            ])->orderBy('first_name')->get()->toArray();

        foreach ($leads as $key => $lead) {
            $signup_ids = [];
            foreach ($lead['signups'] as $signup) {
                $signup_ids[] = $signup['id'];
            }
            $leads[$key]['signups'] = implode(',', $signup_ids);

            $product_ids = [];
            foreach ($lead['products'] as $product) {
                $product_ids[] = $product['id'];
            }
            $leads[$key]['products'] = implode(',', $product_ids);
        }

        $contact_person_detail = \App\Models\Attendee::where('id', $auth_user_id)->first();
        $profileDetail = $profileModel::where('id', $profile_id)->where('event_id', $event['id'])->first();

        $export_list = array();
        $export_list[] = array(
            'Event Name',
            'Contact Person Name',
            $profileKey,
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Company',
            'Title',
            'Rating',
            'Notes',
            'House #',
            'Street #',
            'Postal code',
            'City',
            'Country',
            'Company House #',
            'Company Street #',
            'Company Postal Code',
            'Company City',
            'Company Country',
            'Profile image',
            'Signatures',
            'Products',
            'Signups',
        );


        foreach ($leads as $lead) {
            $lead_city = '';
            $lead_company_name = '';
            $lead_postal_code = '';
            $lead_street_number = '';
            $lead_job_title = '';
            $lead_country = '';
            $lead_phone = '';
            $lead_house_number = '';
            $lead_company_house_number = '';
            $lead_company_postal_code = '';
            $lead_company_street_number = '';
            $lead_company_city = '';
            $lead_company_country = '';


            foreach ($lead['info'] as $info) {
                if ($info['name'] == 'phone') {
                    $phone_number = $info['value'];
                    $phone_number = explode('-', $phone_number);
                    $phone_code = '';
                    $phone_number_actual = '';
                    if (trim($phone_number[0]) != '+' && trim($phone_number[0]) != '') {
                        $phone_code = $phone_number[0];
                    }
                    if (trim($phone_number[1]) != '') {
                        $phone_number_actual = $phone_number[1];
                    }
                    if ($phone_code != '') {
                        $phone_code = '\'' . $phone_code;
                        $lead_phone = $phone_code . '-' . $phone_number_actual;
                    } else {
                        $lead_phone = $phone_number_actual;
                    }
                }
                if ($info['name'] == 'company_name') {
                    $lead_company_name = $info['value'];
                }
                if ($info['name'] == 'job_title') {
                    $lead_job_title = $info['value'];
                }
                if ($info['name'] == 'postal_code') {
                    $lead_postal_code = $info['value'];
                }
                if ($info['name'] == 'street_number') {
                    $lead_street_number = $info['value'];
                }
                if ($info['name'] == 'city') {
                    $lead_city = $info['value'];
                }
                if ($info['name'] == 'country') {
                    $lead_country = $info['value'];
                }
                if ($info['name'] == 'house_number') {
                    $lead_house_number = $info['value'];
                }
                if ($info['name'] == 'company_postal_code') {
                    $lead_company_postal_code = $info['value'];
                }
                if ($info['name'] == 'company_street_number') {
                    $lead_company_street_number = $info['value'];
                }
                if ($info['name'] == 'company_city') {
                    $lead_company_city = $info['value'];
                }
                if ($info['name'] == 'company_country') {
                    $lead_company_country = $info['value'];
                }
                if ($info['name'] == 'company_house_number') {
                    $lead_company_house_number = $info['value'];
                }
            }
            $email = '';
            if ($lead['email']) {
                $email = $lead['email'];
            }
            $characters = array('¿' => 'ø', 'Œ' => 'å', '¾' => 'æ', '¯' => 'Ø', 'Ž' => 'é', '®' => 'Æ', 'â' => 'ä', 'Ÿ' => 'ü', '' => 'å', '' => 'é');

            foreach ($characters as $key => $char) {
                $event_name = str_replace($key, $char, $event['name']);
                $contact_person_f = str_replace($key, $char, $contact_person_detail['first_name']);
                $contact_person_l = str_replace($key, $char, $contact_person_detail['last_name']);
                $first_name = str_replace($key, $char, $lead['first_name']);
                $last_name = str_replace($key, $char, $lead['last_name']);
                $email = str_replace($key, $char, $email);
                $lead_company_name = str_replace($key, $char, $lead_company_name);
                $lead_job_title = str_replace($key, $char, $lead_job_title);
                $lead_street_number = str_replace($key, $char, $lead_street_number);
                $lead_city = str_replace($key, $char, $lead_city);
                $lead_country = str_replace($key, $char, $lead_country);
                $lead_phone = str_replace($key, $char, $lead_phone);
                $lead_postal_code = str_replace($key, $char, $lead_postal_code);
                $lead_house_number = str_replace($key, $char, $lead_house_number);
                $lead_company_house_number = str_replace($key, $char, $lead_company_house_number);
                $lead_company_postal_code = str_replace($key, $char, $lead_company_postal_code);
                $lead_company_street_number = str_replace($key, $char, $lead_company_street_number);
                $lead_company_city = str_replace($key, $char, $lead_company_city);
                $lead_company_country = str_replace($key, $char, $lead_company_country);
                $profileName = str_replace($key, $char, $profileDetail['name']);
            }


            $export_list[] = array(
                'Event Name' => $event_name,
                'Contact Person Name' => $contact_person_f . ' ' . $contact_person_l,
                $profileKey => $profileName,
                'First Name' => $first_name,
                'Last Name' => $last_name,
                'Email' => $email,
                'Phone' => $lead_phone,
                'Company' => $lead_company_name,
                'Title' => $lead_job_title,
                'Rating' => $lead['rating'],
                'Notes' => $lead['notes'],
                'House #' => $lead_house_number,
                'Street #' => $lead_street_number,
                'Postal code' => $lead_postal_code,
                'City' => $lead_city,
                'Country' => $lead_country,
                'Company House #' => $lead_company_house_number,
                'Company Street #' => $lead_company_street_number,
                'Company Postal code' => $lead_company_postal_code,
                'Company City' => $lead_company_city,
                'Company Country' => $lead_company_country,
                'Profile image' => $lead['profile_image_data'] !== '' ? config('app.eventcenter_url') .'/assets/leads/'.$lead['profile_image_data'] : '' ,
                'Signatures' => $lead['image_file'] !== '' ? config('app.eventcenter_url') .'/assets/leads/'.$lead['image_file'] : '',
                'Products' => $lead['products'],
                'Signups' => $lead['signups']
            );
        }

        return $export_list;
    }

    
    /**
     * export
     *
     * @param  mixed $event_id
     * @param  mixed $data
     * @param  mixed $filename
     * @param  mixed $filePath
     * @param  mixed $delemeter
     * @param  mixed $save
     * @param  mixed $utf8
     * @return void
     */
    public function export($event_id, $data, $filename,  $filePath, $delemeter = '', $save = false, $utf8 = false)
    {
        $filename = str_replace(" ", "_", $filename);

        if ($delemeter == '') {
            $event = \App\Models\Event::where('id', $event_id)->first();
            $delimeter = $event->export_setting;
        } else {
            $delimeter = $delemeter;
        }

        $config = new ExporterConfig();
        $config->setDelimiter($delimeter);

        if ($utf8) {
            $config->setFromCharset('UTF-8');
            $config->setToCharset('UTF-8');
        }

        $exporter = new Exporter($config);

        if (!$save) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream; charset=utf-8');
            header("Content-Disposition: attachment; filename=export.csv");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            echo "\xEF\xBB\xBF";
            $exporter->export('php://output', $data);
            exit;
        } else {
            $exporter->export($filePath . $filename, $data);
        }
    }

    
    /**
     * leadSavedEmailTemplate
     *
     * @param  mixed $event_id
     * @param  mixed $id
     * @param  mixed $type_name
     * @param  mixed $contact_person_detail
     * @return void
     */
    public function leadSavedEmailTemplate($event_id, $id, $type_name, $contact_person_detail, $mode)
    {
        $template = \App\Models\LeadHubTemplate::where('event_id', $event_id)->where('type_id', $id)->where('type', $type_name)->first();
        $template = $template['template'];
        $event = \App\Models\Event::find($event_id);
        $language_id = $event['language_id'];
        $subject = $type_name . ' - Your scanned lead - ' . $event['name'];
        if (!$template) {
            $email_template = \App\Models\EventEmailTemplate::where('event_id', '=', $event_id)
                ->where('alias', 'leads_email')->where('type', 'email')->with(['info' => function ($q) use ($language_id) {
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
        }
        $contents = getEmailTemplate($template, $event_id);
        $contents = $this->emailReplaceTags($event, $mode, $type_name, $id, $contact_person_detail, $contents);
        $data['contents'] = $contents;
        $data['subject'] = $subject;

        return $data;
    }
    

    public function getProfileLeaderBoard($formInput)
    {
        if($formInput['mode'] !== "contact_person")
        {
            return [
                "status" => 0,
                "response_type" => INVALID_USER_MODE,
                "message" => "Server setting has been changed please export your leads records and logout",
            ];
        }
        if(!isset($formInput['contact_person_profile'])){
            return [
                "status" => 0,
                "response_type" => CONTANT_PERSON_PROFILE_NOT_PROVIDED,
                "message" => "Contact Person Profile Not Provided",
            ];
        }
        if(!isset($formInput['profile_id'])){
            return [
                "status" => 0,
                "response_type" => PROFILE_ID_NOT_PROVIDED,
                "message" => "Profile ID Not Provided",
            ];
        }
        if($formInput['contact_person_profile'] !== "sponsor" && $formInput['contact_person_profile'] !== "exhibitor"){
            return [
                "status" => 0,
                "response_type" => INVALID_CONTANT_PERSON_PROFILE,
                "message" => "Invalid Contact Person Type",
            ];
        }

        $profileModel = $formInput['contact_person_profile'] === 'sponsor' ? "\App\Models\EventSponsor" : "\App\Models\EventExhibitor";

        $profileModelAttendees = $formInput['contact_person_profile'] === 'sponsor' ? "conf_event_sponsor_attendees" : "conf_event_exhibitor_attendees";
        
        $profileData = $profileModel::where('id', $formInput['profile_id'])->with(['contactPersons' => function($query) use($profileModelAttendees){
            $query->whereNull($profileModelAttendees.'.deleted_at');
        }])->first();
        
        $profileData = $profileData ? $profileData->toArray() : [];

        $contact_persons = [];

        foreach ($profileData['contact_persons'] as $key => $value) {
            $lead_count = \App\Models\EventLead::where('contact_person_type', $formInput['contact_person_profile'])->where('type_id', $formInput['profile_id'])->where('contact_person_id', $value['id'])->count();
                $contact_persons[] =  [
                    'first_name'=> $value['first_name'],
                    'last_name'=> $value['last_name'],
                    'lead_count' => $lead_count
                ];
        }

        return  [
            "status" => 1,
            "data" => [
                "leader_board" => $contact_persons
            ]
        ];

    }

    public function install($request)
    {
        $setting = \App\Models\LeadSetting::where('event_id', $request['from_event_id'])->first();
        if ($setting) {
            $duplicate = $setting->replicate();
            $duplicate->event_id = $request['to_event_id'];
            $duplicate->access_code = rand(100000, 999999);
            $duplicate->save();
        } else {
            \App\Models\LeadSetting::create(array('event_id' => $request['to_event_id'],'access_code'=>rand(100000, 999999),'lead_user_without_contact_person'=>1));
        }
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