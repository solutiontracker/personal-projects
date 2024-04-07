<?php

namespace App\Eventbuizz\Repositories;

use App\Models\EventsiteSetting;

class EventSiteSettingRepository extends AbstractRepository
{
    /**
     * when new event create / cloning event
     *
     * @param array
     */
    public function install($request)
    {
        //For both event copy & clone from template
        //Eventsite settings => Banners
        $from_eventsite_banners = \App\Models\EventSiteBanner::where("event_id", $request['from_event_id'])->get();

        if ($from_eventsite_banners) {
            foreach ($from_eventsite_banners as $from_eventsite_banner) {
                $to_eventsite_banner = $from_eventsite_banner->replicate();
                $to_eventsite_banner->event_id = $request['to_event_id'];
                $to_eventsite_banner->save();

                //info
                $from_eventsite_banner_info = \App\Models\EventSiteBannerInfo::where("banner_id", $from_eventsite_banner->id)->get();
                foreach ($from_eventsite_banner_info as $from_info) {
                    $info = $from_info->replicate();
                    $info->banner_id = $to_eventsite_banner->id;
                    $info->languages_id = $request["languages"]['0'];
                    $info->save();
                }
            }
        }
    }

    /**
     * Fetch eventsite top menus
     * @param array
     */
    public function getEventSiteTopMenus($formData)
    {
        $data = \App\Models\EventSiteModuleOrder::where('event_id', $formData['event_id'])->with([
            'info' => function ($query) use ($formData) {
                return $query->where('languages_id', $formData['language_id']);
            }
        ])
            ->orderBy('sort_order', 'asc')
            ->get();

        $response = array();

        foreach ($data as $key => $val) {
            $response[$key]['id'] = $val['id'];
            $response[$key]['alias'] = $val['alias'];
            $response[$key]['status'] = $val['status'];
            $response[$key]['name'] = (isset($val->info[0]->name) ? $val->info[0]->name : '');
            $response[$key]['value'] = (isset($val->info[0]->value) ? $val->info[0]->value : '');
        }

        return $response;
    }

    /**
     * Update eventsite top menus
     * @param array
     */
    static public function updateEventSiteTopMenus($formInput)
    {
        if (isset($formInput['menus']) && is_array($formInput['menus'])) {
            $sort = 0;
            foreach ($formInput['menus'] as $menu) {
                $menus = \App\Models\EventSiteModuleOrder::where('event_id', $formInput['event_id'])->where('alias', $menu['alias'])->first();
                $menus->sort_order = $sort;
                $menus->status = $menu['status'];
                $menus->save();

                \App\Models\EventSiteModuleOrderInfo::where('languages_id', $formInput['language_id'])
                    ->where('module_order_id', $menus->id)->update([
                        "value" => $menu['value']
                    ]);
                $sort++;
            }
        }
    }

    /**
     * update eventsite banners
     * @param array
     *
     */
    public function updateEventSiteBanners($formInput)
    {
        $event = \App\Models\Event::find($formInput['event_id']);
        $fields = array('title', 'message');
        $languages = get_event_languages($event->id);
        if (isset($formInput['eventsite_banners']) && $formInput['eventsite_banners']) {
            foreach ($formInput['eventsite_banners'] as $key => $banner_img) {
                $image = 'event_site_banner_' . time() . $key . '.' . $banner_img->getClientOriginalExtension();
                $banner_img->storeAs('/assets/eventsite_banners', $image);

                //clone image
                moveFile(
                    storage_path('app/assets/eventsite_banners/' . $image),
                    config('cdn.cdn_upload_path') . 'assets/eventsite_banners/' . $image
                );

                $banner = \App\Models\EventSiteBanner::create([
                    'event_id' => $event->id,
                    'status' => 1,
                    'image' => $image
                ]);

                //info
                $info = array();
                foreach ($languages as $language) {
                    foreach ($fields as $field) {
                        $info[] = new \App\Models\EventSiteBannerInfo(array(
                            'name' => $field,
                            'value' => (isset($formInput[$field]) ? $formInput[$field] : ''),
                            'banner_id' => $banner->id, 'languages_id' => $language,
                            'status' => 1
                        ));
                    }
                }
                $banner->info()->saveMany($info);
            }
        }
        return $this;
    }

    /**
     * setting
     * @param array
     *
     */
    static public function getSetting($formInput)
    {
        return \App\Models\EventsiteSetting::where('event_id', $formInput["event_id"])->where('registration_form_id', 0)->first();;
    }
    
    /**
     * getPaymentSetting
     *
     * @param  mixed $formInput
     * @return void
     */
    public static function getPaymentSetting($formInput)
    {
        $query = \App\Models\EventsitePaymentSetting::where('event_id', $formInput['event_id']);

        if(isset($formInput['registration_form_id']) && $formInput['registration_form_id']) {
            $query->where('registration_form_id', $formInput['registration_form_id']);  
        } else {
            $query->where('registration_form_id', 0);  
        }
        
        return $query->first();
    }

    /**
     * get payment cards
     * @param array
     *
     */
    static public function getPaymentCards($formInput)
    {
        return \App\Models\EventCardType::where('event_id', $formInput['event_id'])->first();
    }

    /**
     * save payment setting
     * @param array
     *
     */
    public function savePaymentSetting($formInput)
    {
        $settings = \App\Models\EventsitePaymentSetting::where('event_id', $formInput["event_id"])->where('registration_form_id', 0)->first();

        if ($formInput["type"] == "payment-providers") {
            $mistertango_markets = array();
            foreach ($formInput['mistertango_markets'] as $market) {
                array_push($mistertango_markets, $market['value']);
            }
            $formInput['mistertango_markets'] = $mistertango_markets;

            $swed_bank_region = array();
            foreach ($formInput['swed_bank_region'] as $region) {
                array_push($swed_bank_region, $region['value']);
            }
            $formInput['swed_bank_region'] = $swed_bank_region;

            $swed_bank_language = array();
            foreach ($formInput['swed_bank_language'] as $language) {
                array_push($swed_bank_language, $language['value']);
            }
            $formInput['swed_bank_language'] = $swed_bank_language;

            $settings->mistertango_markets = implode(',', $formInput['mistertango_markets'] ?? []);
            $settings->swed_bank_password = $formInput['swed_bank_password'];
            $settings->swed_bank_region = implode(',', $formInput['swed_bank_region'] ?? []);
            $settings->swed_bank_language = implode(',', $formInput['swed_bank_language'] ?? []);
            $settings->qp_agreement_id  = $formInput['qp_agreement_id'];
            $settings->qp_secret_key  = $formInput['qp_secret_key'];
            $settings->qp_auto_capture  = (isset($formInput['qp_auto_capture']) && $formInput['qp_auto_capture'] ? 1 : 0);
            $settings->wc_customer_id  = $formInput['wc_customer_id'];
            $settings->wc_secret  = $formInput['wc_secret'];
            $settings->wc_shop_id  = $formInput['wc_shop_id'];
            $settings->stripe_api_key  = $formInput['stripe_api_key'];
            $settings->stripe_secret_key  = $formInput['stripe_secret_key'];
            $settings->billing_merchant_type  = $formInput['billing_merchant_type'];
            $settings->eventsite_merchant_id  = $formInput['eventsite_merchant_id'];
            $settings->eventsite_order_prefix  = $formInput['eventsite_order_prefix'];
            $settings->SecretKey  = $formInput['SecretKey'];
            $settings->billing_yourpay_language  = $formInput['billing_yourpay_language'];
            $settings->bambora_secret_key  = $formInput['bambora_secret_key'];
            $settings->save();
        } else if ($formInput["type"] == "invoice-setting") {
            $eventsite_vat_countries = array();
            foreach ($formInput['eventsite_vat_countries'] as $country) {
                array_push($eventsite_vat_countries, $country['value']);
            }
            $formInput['eventsite_vat_countries'] = $eventsite_vat_countries;

            $settings->eventsite_vat_countries = implode(',', $formInput['eventsite_vat_countries'] ?? []);
            $settings->bcc_emails = implode(',', $formInput['bcc_emails'] ?? []);
            $settings->eventsite_currency = $formInput['eventsite_currency'];
            $settings->eventsite_invoice_prefix = $formInput['eventsite_invoice_prefix'];
            $settings->eventsite_invoice_no = $formInput['eventsite_invoice_no'];
            $settings->eventsite_vat = $formInput['eventsite_vat'];
            $settings->eventsite_apply_multi_vat = $formInput['eventsite_apply_multi_vat'];
            $settings->eventsite_always_apply_vat = $formInput['eventsite_always_apply_vat'];
            $settings->eventsite_billing_fik = $formInput['eventsite_billing_fik'];
            $settings->save();
        } else if ($formInput["type"] == "ean-setting") {
            $settings->auto_invoice = ($formInput['auto_invoice'] ? $formInput['auto_invoice'] : 0);
            $settings->account_number = $formInput['account_number'];
            $settings->bank_name = $formInput['bank_name'];
            $settings->payment_date = $formInput['payment_date'];
            $settings->save();
        } else if ($formInput["type"] == "fik-setting") {
            $settings->invoice_type = $formInput['invoice_type'];
            $settings->debitor_number = $formInput['debitor_number'];
            $settings->save();
        } else if ($formInput["type"] == "payment-methods") {
            $settings->eventsite_billing_fik = $formInput['eventsite_billing_fik'];
            $settings->save();
        }
    }

    /**
     * save payment cards
     * @param array
     *
     */
    public function savePaymentCards($formInput)
    {
        if ($formInput["type"] == "payment-providers") {
            $payment_cards = array();
            $organizer_id = organizer_id();
            foreach ($formInput['payment_cards'] as $card) {
                if ($card['isChecked']) {
                    array_push($payment_cards, $card['id']);
                }
            }
            $cardAssign = serialize($payment_cards);
            $paymentCard =  \App\Models\EventCardType::where('event_id', $formInput['event_id'])->first();
            if ($paymentCard) {
                $paymentCard->card_type = $cardAssign;
                $paymentCard->save();
            } else {
                $paymentCard =  \App\Models\EventCardType::create([
                    "event_id" => $formInput['event_id'],
                    "organizer_id" => $organizer_id,
                    "card_type" => $cardAssign
                ]);
            }
        } else if ($formInput["type"] == "purchase-policy") {
            $paymentCard =  \App\Models\EventCardType::where('event_id', $formInput['event_id'])->first();
            if ($paymentCard) {
                $paymentCard->purchase_policy_inline_text = $formInput["purchase_policy_inline_text"];
                $paymentCard->purchase_policy = $formInput["purchase_policy"];
                $paymentCard->save();
            }
        }
        return $paymentCard;
    }

    /**
     * Billing Sections
     * @param array
     *
     */
    public function getSectionsData($formInput)
    {
        //check if data available or not
        
        $count = \App\Models\BillingField::where(['event_id'=> $formInput["event_id"],'registration_form_id'=> $formInput['registration_form_id']])->count();
        if ($count < 1) {
            $this->insertFieldsRecord($formInput["event_id"]);
        }
        $moduleData = \App\Models\BillingField::where(['event_id'=> $formInput["event_id"], 'registration_form_id' => $formInput['registration_form_id']])->where('type', '=', 'section')->where('status', '=', 1)->with(['info' => function ($query) use ($formInput) {
            return $query->where('languages_id', $formInput["language_id"]);
        }])->orderBy('sort_order', 'asc')->get();
        return $moduleData;
    }

    /**
     * Billing Section => Fields
     * @param array
     * @param string
     *
     */
    public function getSectionFields($formInput, $section_alias)
    {
        return  \App\Models\BillingField::where('event_id', $formInput["event_id"])->where('type', 'field')->where('section_alias', $section_alias)->with(['info' => function ($query) use ($formInput) {
            return $query->where('languages_id', $formInput["language_id"]);
        }])->orderBy('sort_order', 'asc')->get();
    }

    /**
     * Billing all section fields
     * @param array
     *
     */
    public static function getAllSectionFields($formInput)
    {
        $fields = array();
        $record = \App\Models\BillingField::where('event_id', $formInput["event_id"])->where('type', 'field')->with(['info' => function ($query) use ($formInput) {
            return $query->where('languages_id', $formInput["language_id"]);
        }])->orderBy('sort_order', 'asc')->get();
        foreach ($record as $key => $field) {
            if ($field->section_alias) {
                $fields[$field->section_alias][$field->field_alias]["status"] = $field->status;
                $fields[$field->section_alias][$field->field_alias]["name"] = (isset($field->info[0]->value) ? $field->info[0]->value : '');
            }
        }
        return $fields;
    }

    /**
     * Insert Billing Sections => Fields
     * @param array
     * @param int
     *
     */
    public function insertFieldsRecord($event_id)
    {
        $fields = \App\Models\Field::with('info')->get();
        $event_fields = \App\Models\BillingField::where('event_id', $event_id)->get();
        foreach ($fields as $field) {
            $found = false;
            foreach ($event_fields as $eventField) {
                if ($eventField['field_alias'] == $field['field_alias']) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $section_data = ['sort_order' => $field['sort_order'], 'type' => $field['type'], 'status' => $field['status'], 'event_id' => $event_id, 'field_alias' => $field['field_alias'], 'section_alias' => $field['section_alias'], 'mandatory' => $field['mandatory']];
                $eventSetting        =  \App\Models\BillingField::create($section_data);
                $billingFieldInfo = array();
                foreach ($field['info'] as $field_info) {
                    $billingFieldInfo[] = new \App\Models\BillingFieldInfo(['name' => 'name', 'value' => $field_info['value'], 'languages_id' => $field_info['languages_id']]);
                }
                $eventSetting->info()->saveMany($billingFieldInfo);
            }
        }
        $organizer_id = organizer_id();
        if ($organizer_id == '161') {
            $field_label =   \App\Models\BillingField::where('event_id', '=', $event_id)->where('field_alias', '=', 'interests')->first();
            \App\Models\BillingFieldInfo::where("field_id", $field_label->id)->update([
                "value" => 'Comments'
            ]);
        }
    }

    /**
     * Update => Billing Section => Fields
     * @param array
     *
     */
    public function updateSectionFields($formInput)
    {
        foreach ($formInput["sections_data"] as $i => $section) {
            foreach ($section["fields"] as $field) {
                \App\Models\BillingField::where('event_id', $formInput["event_id"])->where('id', $field["id"])->update([
                    "status" => $field["status"]
                ]);
            }
        }
    }

    /**
     * Billing all section fields
     * @param array
     *
     */
    static public function getAllSections($formInput)
    { 
        $query = \App\Models\BillingField::where('event_id', $formInput["event_id"])->where('type', 'section');

        if(isset($formInput['field_alias'])) {
            $query->where('field_alias', $formInput['field_alias']); 
        }

        $query->where('registration_form_id', isset($formInput['registration_form_id']) ? $formInput['registration_form_id'] : 0); 

        $query->where('status', 1);

        $sections = $query->with(['info' => function ($query) use ($formInput) {
            return $query->where('languages_id', $formInput["language_id"]);
        }])->orderBy('sort_order', 'ASC')->get()->toArray();

        foreach ($sections as $key => $section) {
            $info = readArrayKey($section, [], 'info');
            $sections[$key]['detail'] = $info;
            $fields = \App\Models\BillingField::where('event_id', $formInput['event_id'])
                    ->where('type', 'field')
                    ->where('status', 1)
                    ->where('registration_form_id', isset($formInput['registration_form_id']) ? $formInput['registration_form_id'] : 0)
                    ->where('section_alias', $section['field_alias'])
                    ->with(['info' => function ($query) use ($formInput) {
                        return $query->where('languages_id', $formInput['language_id']);
                    }])->orderBy('sort_order', 'asc')->get()->toArray();

            foreach ($fields as $field) {
                $info = readArrayKey($field, [], 'info');
                $field['detail'] = $info;
                $sections[$key]['fields'][] = $field;
            }
        }

        return $sections;
    }

    /**
     * Event waitinglist setting
     * @param array
     *
     */
    public static function getWaitingListSetting($formInput)
    {
        $registration_form_id = isset($formInput['registration_form_id']) ? $formInput['registration_form_id'] : 0;

        return \App\Models\EventWaitingListSetting::where('event_id', $formInput['event_id'])->where('registration_form_id', $registration_form_id)->first();
    }
    
    /**
     * validateEan
     *
     * @param  mixed $ean
     * @return void
     */
    public static function validateEan($ean)
    {
        $content = file_get_contents("http://registration.nemhandel.dk/NemHandelRegisterWeb/public/participant/info?keytype=GLN&key=$ean&asXML=true");

        $xml = simplexml_load_string($content);

        $ean = \App\Models\EanNumber::where('ean', $ean)->first();

        if($xml) {
            return ['status'=> '1', 'message' => 'EAN no is valid.', 'show_billing_bruger_id' => !$ean ? false : true];
        }

        return ['status'=> '0', 'message' => 'Ean no is not valid.'];
    }
    
    /**
     * validateCvr
     *
     * @param  mixed $cvr
     * @return void
     */
    static public function validateCvr($cvr)
    {
        $vat = preg_replace('/[^0-9]/', '', $cvr);
        
        if(!empty($vat)) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://cvrapi.dk/api?search=' . $vat . '&country=dk');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, 'eventbuizz');
            $response = curl_exec($ch);
            curl_close($ch);
            $response = json_decode($response, true);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if(isset($response['error']) && $response['error'] != "QUOTA_EXCEEDED") {
                return false;
            }
            return true;
        }
        
        return false;
    }
    
    /**
     * getRegistrationForm
     *
     * @param  mixed $formInput
     * @return void
     */
    public static function  getRegistrationForm($formInput) {
        return \App\Models\RegistrationForm::where('event_id', $formInput['event_id'])->where('type_id', $formInput['type_id'])->first();
    }

    /**
     * getRegistrationForms
     *
     * @param  mixed $formInput
     * @return void
     */
    public static function  getRegistrationForms($formInput) {
        
        $forms = array();

        $query = \App\Models\RegistrationForm::join('conf_event_attendee_type', 'conf_event_attendee_type.id', '=', 'conf_registration_form.type_id')->where('conf_registration_form.status', 1)->where('conf_registration_form.event_id', $formInput['event_id'])->orderBy('conf_event_attendee_type.sort_order')->orderBy('conf_event_attendee_type.attendee_type')->select('conf_registration_form.*', 'conf_event_attendee_type.attendee_type as attendee_type');

        if(isset($formInput['attendee_types']) && $formInput['attendee_types']) {
            $query->whereIn('conf_registration_form.type_id', explode(',', $formInput['attendee_types']));
        }

        $results = $query = $query->get();

        foreach($results as $result) {
            $forms[] = array(
                "id" => $result->type_id,
                "name" => $result->attendee_type,
                "registration_form_id" => $result->id,
            );
        }

        return $forms;
    }

    /**
     * getDefaultRegistrationFormId
     *
     * @param  mixed $formInput
     * @return void
     */
    public static function getDefaultRegistrationFormId($formInput) {
        $result = \App\Models\RegistrationForm::has('attendee_type')->with(['attendee_type'])->where('status', 1)->where('event_id', $formInput['event_id'])->where('is_default', 1)->first();
        if($result) {
            $form = array(
                "id" => $result->attendee_type->id,
                "name" => $result->attendee_type->attendee_type,
                "registration_form_id" => $result->id,
            );
            return $form;
        } else {
            return null;
        }
    }

    /**
     * getDefaultRegistrationFormIdByAttendeeType
     *
     * @param  mixed $formInput
     * @return void
     */
    public static function getDefaultRegistrationFormIdByAttendeeType($formInput) {
        $result = \App\Models\RegistrationForm::has('attendee_type')->with(['attendee_type'])->where('event_id', $formInput['event_id'])->where('type_id', $formInput['type_id'])->first();
        if($result) {
            $form = array(
                "id" => $result->attendee_type->id,
                "name" => $result->attendee_type->attendee_type,
                "registration_form_id" => $result->id,
            );
            return $form;
        } else {
            return null;
        }
    }

    /**
     * getRegistrationFormById
     *
     * @param  mixed $formInput
     * @return void
     */
    public static function  getRegistrationFormById($formInput) {
        return \App\Models\RegistrationForm::where('event_id', $formInput['event_id'])->where('id', $formInput['id'])->first();
    }

    /**
     * getRegistrationFormByAttendeeType
     *
     * @param  mixed $formInput
     * @return void
     */
    public static function  getRegistrationFormByAttendeeType($formInput) {
        return \App\Models\RegistrationForm::where('event_id', $formInput['event_id'])->where('type_id', $formInput['type_id'])->first();
    }

    /**
     * getEventNewsSubscriberSetting
     * @param array
     *
     */
    static public function getEventNewsSubscriberSetting($formInput)
    {
        $subscriber_detail = ['status' => 0, 'subscriber_list' => []];
        $subscriber =  \App\Models\EventNewsSubscriberSetting::where('event_id', $formInput["event_id"])->first();
        if($subscriber && $subscriber->subscriber_ids) {
            $subscriber_detail['status'] = $subscriber->status;
            $subscriberList = json_decode($subscriber->subscriber_ids, true); 
            if(count($subscriberList) > 0) {
                $resultList = [];
                $subscriberListIds = array_column($subscriberList, 'id');
                $mailingList = \App\Models\MailingList::where('organizer_id', '=', $formInput['organizer_id'])->whereIn('id', $subscriberListIds)->get();
                foreach ($mailingList as $list) {
                    $key = array_search($list->id, $subscriberListIds);
                    $resultList[] = [
                        'id' => $list->id,
                        'name' => $subscriberList[$key]['label']
                    ];
                }
                $subscriber_detail['subscriber_list'] = $resultList;
            }
        }

        return $subscriber_detail;
    }
    
    /**
     * getAttendeeRegistrationFormId
     *
     * @param  mixed $event_id
     * @return void
     */
    public static function getAttendeeRegistrationFormByAlias($event_id, $alias) {

        $attendee_type_id = \App\Models\EventAttendeeType::where('event_id', $event_id)->where('alias', $alias)->value('id');

        $result = \App\Models\RegistrationForm::where('event_id', $event_id)->where('type_id', $attendee_type_id)->first();

        if ($result) {
            return $result->id;
        } else {
            return 0;
        }

    }
}
