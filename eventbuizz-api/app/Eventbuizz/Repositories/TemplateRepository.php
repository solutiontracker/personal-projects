<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;

class TemplateRepository extends AbstractRepository
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function copyTemplates($types, $from_event_id, $to_event_id, $languages)
    {
        foreach ($types as $type) {
            foreach ($languages as $language) {
                $from = \App\Models\EventEmailTemplate::where('event_id', '=', $from_event_id)->where('type', '=', $type)->with(['info' => function ($query) use ($language) {
                    return $query->where('languages_id', '=', $language);
                }])->get();
                if ($from) {
                    foreach ($from as $template) {
                        $to = \App\Models\EventEmailTemplate::where('event_id', '=', $to_event_id)->where('alias', '=', $template['alias'])->first();
                        if ($to) {
                            foreach ($template['info'] as $info) {
                                $templateInfo = \App\Models\EmailTemplateInfo::where('template_id', '=', $to['id'])->where('languages_id', '=', $language)->where('name', '=', $info['name'])->first();

                                $templateInfoObject = \App\Models\EmailTemplateInfo::find($templateInfo['id']);

                                if (in_array($templateInfo['name'], ['template', 'content']) && $from_event_id < config('setting.template_builder_start_event_id') && $to_event_id >= config('setting.template_builder_start_event_id')) {
                                    if ($templateInfo['name'] == "template") {
                                        $templateInfoObject->value = $info['value'];
                                        $templateInfoObject->save();

                                        \App\Models\EmailTemplateInfo::where('template_id', $templateInfo['template_id'])->where('name', 'content')->update([
                                            'value' => str_replace(
                                                '{code}',
                                                str_replace('width="680"', 'width="600"', str_replace('padding: 15px', 'padding: 0px', $info['value'])),
                                                \View::make('admin.templates.defaultTemplateBuilderCustomContent')->render()
                                            ),
                                        ]);
                                    }
                                } else if (($from_event_id < config('setting.template_builder_start_event_id') && $to_event_id < config('setting.template_builder_start_event_id')) || ($from_event_id >= config('setting.template_builder_start_event_id') && $to_event_id >= config('setting.template_builder_start_event_id'))) {
                                    $templateInfoObject->value = $info['value'];
                                    $templateInfoObject->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function install($request)
    {
        $this->createDefaultTemplates($request['to_event_id'], $request['languages']);

        //Registration form templates
        $templates = \App\Models\RegistrationFormTemplate::where("event_id", $request['from_event_id'])->get();
        
        //Delete old 
        \App\Models\RegistrationFormTemplate::where("event_id", $request['to_event_id'])->delete();

        foreach ($templates as $template) {

            $to_template = $template->replicate();

            if (session()->has('clone.event.event_registration_form.' . $template->registration_form_id) && $template->registration_form_id > 0) {
                $to_template->registration_form_id = session()->get('clone.event.event_registration_form.' . $template->registration_form_id);
            }

            $to_template->event_id = $request['to_event_id'];

            $to_template->save();

        }
    }

    public function createDefaultTemplates($event_id, $languages)
    {
        $event_template_instance = new \App\Models\EventEmailTemplate();

        $model_object_template_info = new \App\Models\EmailTemplateInfo();

        $master_templates = \App\Models\TemplateMaster::whereIn('language_id', $languages)->get();

        if (count($master_templates) > 0) {

            $event_templates = $event_template_instance->where('event_id', '=', $event_id)->get();

            $not_found = array();

            foreach ($master_templates as $master_template) {
                $found = false;
                foreach ($event_templates as $event_template) {
                    if ($event_template['alias'] == $master_template['alias']) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $not_found[] = $master_template;
                }
            }

            $model_id = '';

            foreach ($not_found as $new_template) {

                $model_id = $event_template_instance->create(array(
                    'event_id' => $event_id,
                    'alias' => $new_template['alias'],
                    'type' => $new_template['type']
                ));

                foreach ($languages as $lang) {
                    $model_object_template_info->create(array(
                        'template_id' => $model_id['id'],
                        'status' => '1',
                        'languages_id' => $lang,
                        'name' => 'title',
                        'value' => $new_template['title']
                    ));
                    $model_object_template_info->create(array(
                        'template_id' => $model_id['id'],
                        'status' => '1',
                        'languages_id' => $lang,
                        'name' => 'subject',
                        'value' => $new_template['subject']
                    ));
                    $model_object_template_info->create(array(
                        'template_id' => $model_id['id'],
                        'status' => '1',
                        'languages_id' => $lang,
                        'name' => 'template',
                        'value' => $new_template['template']
                    ));
                    $model_object_template_info->create(array(
                        'template_id' => $model_id['id'],
                        'status' => '1',
                        'languages_id' => $lang,
                        'name' => 'content',
                        'value' => ($new_template['content'] ? $new_template['content'] : '')
                    ));
                }
            }
        }
    }

    /**
     *template listing
     * @param array
     */
    public function listing($formInput)
    {
        $email_templates = $this->getTemplates($formInput, 'email', ['registration_invite', 'attendee', 'registration_verification', 'attendee_cancel_registration', 'waiting_list_registration_invite', 'attendee_reminder_email', 'waiting_list_registration_confirmation', 'native_app_reset_password']);
        $invite_group_email = ['registration_invite', 'attendee', 'registration_verification', 'attendee_cancel_registration'];
        $profile_group_email = ['sponsor', 'exhibitor', 'speaker', 'attendee_profile_email'];
        $reminder_group_email = [
            'poll_reminder_email', 'survey_reminder_email', 'attendee_reminder_email',
            'sub_reg_reminder_email', 'checkin_attendee_invite', 'invoice_reminder_email'
        ];
        $reservation_group_email = [
            'reservation_reject_email', 'reservation_accept_email', 'reservation_cancel_email', 'contact_person_reservation_cancel_email',
            'reservation_request_email', 'reservation_new_request_email'
        ];

        $waiting_list_templates = ['waiting_list_registration_confirmation', 'waiting_list_registration_invite'];

        $mydocument_group_email = ['document', 'share_document', 'attendee_document'];

        $email_templates_invite = array();
        $email_templates_profile = array();
        $email_templates_reminder = array();
        $email_templates_misc = array();
        $email_templates_mydocument = array();
        $email_templates_reservation = array();
        $email_templates_waitinglist = array();
        foreach ($email_templates as $e_template) {
            if (in_array($e_template['template']['alias'], $invite_group_email)) {
                $email_templates_invite[] = $e_template;
            } elseif (in_array($e_template['template']['alias'], $profile_group_email)) {
                $email_templates_profile[] = $e_template;
            } elseif (in_array($e_template['template']['alias'], $reminder_group_email)) {
                $email_templates_reminder[] = $e_template;
            } elseif (in_array($e_template['template']['alias'], $reservation_group_email)) {
                $email_templates_reservation[] = $e_template;
            } elseif (in_array($e_template['template']['alias'], $waiting_list_templates)) {
                $email_templates_waitinglist[] = $e_template;
            } elseif (in_array($e_template['template']['alias'], $mydocument_group_email)) {
                $email_templates_mydocument[] = $e_template;
            } else {
                $email_templates_misc[] = $e_template;
            }
        }

        $templateIds = array();
        $emails_inivtes = array();
        foreach ($email_templates_invite as $etemplate) {
            $emails_inivtes[$etemplate['template']['alias']]  = array(
                'id' => $etemplate['template']['id'],
                'display' => $etemplate['info']['title'],
                'url' => "template/edit/" . $etemplate['template']['id'],
                'alias' => $etemplate['template']['alias']
            );
            array_push($templateIds, $etemplate['template']['id']);
        }
        $emails_profiles = array();
        foreach ($email_templates_profile as $etemplate) {
            $emails_profiles[$etemplate['template']['alias']]  = array(
                'id' => $etemplate['template']['id'],
                'display' => $etemplate['info']['title'],
                'url' => "template/edit/" . $etemplate['template']['id'],
                'alias' => $etemplate['template']['alias']
            );
            array_push($templateIds, $etemplate['template']['id']);
        }

        $emails_reminders = array();
        foreach ($email_templates_reminder as $etemplate) {
            $emails_reminders[$etemplate['template']['alias']]  = array(
                'id' => $etemplate['template']['id'],
                'display' => $etemplate['info']['title'],
                'url' => "template/edit/" . $etemplate['template']['id'],
                'alias' => $etemplate['template']['alias']
            );
            array_push($templateIds, $etemplate['template']['id']);
        }

        $emails_mydocument = array();
        foreach ($email_templates_mydocument as $etemplate) {
            $emails_mydocument[$etemplate['template']['alias']]  = array(
                'id' => $etemplate['template']['id'],
                'display' => $etemplate['info']['title'],
                'url' => "template/edit/" . $etemplate['template']['id'],
                'alias' => $etemplate['template']['alias']
            );
            array_push($templateIds, $etemplate['template']['id']);
        }

        $emails_misc = array();
        foreach ($email_templates_misc as $etemplate) {
            $emails_misc[$etemplate['template']['alias']]  = array(
                'id' => $etemplate['template']['id'],
                'display' => $etemplate['info']['title'],
                'url' => "template/edit/" . $etemplate['template']['id'],
                'alias' => $etemplate['template']['alias']
            );
            array_push($templateIds, $etemplate['template']['id']);
        }

        $emails_reservation = array();
        foreach ($email_templates_reservation as $etemplate) {
            $emails_reservation[$etemplate['template']['alias']]  = array(
                'id' => $etemplate['template']['id'],
                'display' => $etemplate['info']['title'],
                'url' => "template/edit/" . $etemplate['template']['id'],
                'alias' => $etemplate['template']['alias']
            );
            array_push($templateIds, $etemplate['template']['id']);
        }

        $emails_waitinglist = array();
        foreach ($email_templates_waitinglist as $etemplate) {
            $emails_waitinglist[$etemplate['template']['alias']]  = array(
                'id' => $etemplate['template']['id'],
                'display' => $etemplate['info']['title'],
                'url' => "template/edit/" . $etemplate['template']['id'],
                'alias' => $etemplate['template']['alias']
            );
            array_push($templateIds, $etemplate['template']['id']);
        }

        $sms_templates = self::getTemplates($formInput, 'sms', []);
        $reminder_group_sms = [
            'attendee_reminder_sms', 'survey_reminder_sms', 'poll_reminder_sms',
            'sub_reg_reminder_sms', 'checkin_attendee_sms'
        ];
        $invite_group_sms = ['mobile_app_link', 'attendee_invite_sms', 'attendee_sms'];
        $reservation_group_sms = [
            'reservation_reject_sms', 'reservation_accept_sms', 'reservation_cancel_sms', 'contact_person_reservation_cancel_sms',
            'reservation_request_sms', 'reservation_new_request_sms'
        ];
        $misc_group_sms = ['native_app_reset_password_sms'];

        $sms_templates_invite = array();
        $sms_templates_reminder = array();
        $sms_templates_reservation = array();
        $sms_templates_misc = array();

        foreach ($sms_templates as $s_template) {
            if (in_array($s_template['template']['alias'], $invite_group_sms)) {
                $sms_templates_invite[$s_template['template']['alias']] = $s_template;
            } elseif (in_array($s_template['template']['alias'], $reminder_group_sms)) {
                $sms_templates_reminder[] = $s_template;
            } elseif (in_array($s_template['template']['alias'], $reservation_group_sms)) {
                $sms_templates_reservation[] = $s_template;
            } elseif (in_array($s_template['template']['alias'], $misc_group_sms)) {
                $sms_templates_misc[] = $s_template;
            }
        }

        $sms_inivtes = array();
        foreach ($sms_templates_invite as $stemplate) {
            $sms_inivtes[$stemplate['template']['alias']]  = array(
                'id' => $stemplate['template']['id'],
                'display' => $stemplate['info']['title'],
                'url' => "template/edit/" . $stemplate['template']['id'],
                'alias' => $stemplate['template']['alias']
            );
        }

        $sms_reservations = array();
        foreach ($sms_templates_reservation as $stemplate) {
            $sms_reservations[$stemplate['template']['alias']]  = array(
                'id' => $stemplate['template']['id'],
                'display' => $stemplate['info']['title'],
                'url' => "template/edit/" . $stemplate['template']['id'],
                'alias' => $stemplate['template']['alias']
            );
        }

        $sms_reminders = array();
        foreach ($sms_templates_reminder as $stemplate) {
            $sms_reminders[$stemplate['template']['alias']]  = array(
                'id' => $stemplate['template']['id'],
                'display' => $stemplate['info']['title'],
                'url' => "template/edit/" . $stemplate['template']['id'],
                'alias' => $stemplate['template']['alias']
            );
        }

        $sms_miscs = array();
        foreach ($sms_templates_misc as $stemplate) {
            $sms_miscs[$stemplate['template']['alias']]  = array(
                'id' => $stemplate['template']['id'],
                'display' => $stemplate['info']['title'],
                'url' => "template/edit/" . $stemplate['template']['id'],
                'alias' => $stemplate['template']['alias']
            );
        }


        $menus = array(
            "default_template_id" => (isset($email_templates[0]['template']['id']) ? $email_templates[0]['template']['id'] : Null),
            'email' => array(
                'display' => 'Email templates',
                'url' => '_admin/templates',
                'sub' => array(
                    'invites_email' => array(
                        'display' => 'Invites',
                        'url' => '#',
                        'sub' => $emails_inivtes
                    ),
                    'profiles_email' => array(
                        'display' => 'Profiles',
                        'url' => '#',
                        'sub' => $emails_profiles
                    ),
                    'reminders_email' => array(
                        'display' => 'Reminders',
                        'url' => '#',
                        'sub' => $emails_reminders
                    ),
                    'mydocument_email' => array(
                        'display' => 'My document',
                        'url' => '#',
                        'sub' => $emails_mydocument
                    ),
                    'misc_email' => array(
                        'display' => 'Misc',
                        'url' => '#',
                        'sub' => $emails_misc
                    ),
                    'reservation_email' => array(
                        'display' => 'Reservations',
                        'url' => '#',
                        'sub' => $emails_reservation
                    ),
                    'waitinglist_email' => array(
                        'display' => 'Waiting list',
                        'url' => '#',
                        'sub' => $emails_waitinglist
                    )
                )
            ),
            'sms' => array(
                'display' => 'SMS templates',
                'url' => '#',
                'sub' => array(
                    'invites_sms' => array(
                        'display' => 'Invites',
                        'url' => '#',
                        'sub' => $sms_inivtes
                    ),
                    'profiles_email' => array(
                        'display' => 'Reminders',
                        'url' => '#',
                        'sub' => $sms_reminders
                    ),
                    'reservation_sms' => array(
                        'display' => 'Reservations',
                        'url' => '#',
                        'sub' => $sms_reservations
                    ),
                    'misc_sms' => array(
                        'display' => 'Misc',
                        'url' => '#',
                        'sub' => $sms_miscs
                    )
                )
            ),
            'email-marketing' => array(
                'display' => 'Email marketing',
                'url' => '#',
                'sub' => array(
                    'campaigns' => array(
                        'display' => 'Campaigns',
                        'url' => '_admin/campaigns/'
                    ),
                    'templates' => array(
                        'display' => 'Templates',
                        'url' => url('_admin/eventEmailMarketingTemplates/')
                    )
                )
            ),
            'templateIds' => $templateIds
        );
        return $menus;
    }

    /**
     *event templates
     * @param array
     */
    public function getTemplates($formInput, $type, $alias = [])
    {
        $templates = \App\Models\EventEmailTemplate::where('event_id', $formInput['event_id'])->whereIn('alias', $alias)->where('type', $type)->get();
        $return_array = array();
        foreach ($templates as $template) {
            $information = \App\Models\EventEmailTemplate::find($template->id)->info()->where('template_id', $template->id)
                ->where('languages_id', $formInput['language_id'])->get();
            $fields = array();
            foreach ($information as $info) {
                $fields[$info->name] = $info->value;
            }
            $return_array[] = array('template' => $template, 'info' => $fields);
        }
        return $return_array;
    }

    /**
     *event template data
     * @param array
     * @param int
     */
    public function getTemplateData($formInput, $id)
    {
        $dynamic_fields_array = [];

        $template = \App\Models\EventEmailTemplate::find($id);

        $info_fields = array();

        if($formInput['is_new_flow'] == 1 && in_array($template->alias, ['registration_invite', 'registration_verification', 'attendee_cancel_registration', 'waiting_list_registration_invite', 'attendee_reminder_email', 'waiting_list_registration_confirmation'])) {

            $template_info = \App\Models\RegistrationFormTemplate::where(['event_id'=> $formInput['event_id'],'registration_form_id'=> $formInput['registration_form_id']])->where('alias', $template->alias)->first();
            
            if($template_info) {

                $info_fields['template'] = $template_info->template;

                $info_fields['content'] = $template_info->content;

                $info_fields['subject'] = $template_info->subject;

                $info_fields['title'] = $template_info->title;

            }

        } else {

            $template_info = $template->info()->where('template_id', $id)
                ->where('languages_id', $formInput['language_id'])->get();

            foreach ($template_info as $info) {
                $info_fields[$info['name']] = $info['value'];
            }

        }

        $dynamic_fields = $this->getDynamicFieldsList($template['alias']);

        foreach ($dynamic_fields as $key => $field) {
            $dynamic_fields_array[$key]['name'] = ucwords(str_replace('_', ' ', str_replace('{', ' ', str_replace('}', ' ', $field))));
            $dynamic_fields_array[$key]['field'] = $field;
        }

        $style = email_background_color($formInput['event_id']);

        $type = ($formInput['event_id'] < config('setting.template_builder_start_event_id') ? 'old' : 'new');

        $return_array = array(
            'id' => $id, 'alias' => $template->alias, 'template' => $template, 'info' => $info_fields, 'dynamic_fields' => $dynamic_fields_array, 'style' => $style['css'], 'type' => $type
        );

        return $return_array;
        
    }

    public function getDynamicFieldsList($alias, $all = FALSE)
    {

        $template_array = array();
        $template_array['registration_invite'] = array(
            '{event_logo}', '{attendee_name}', '{first_name}', '{last_name}', '{attendee_email}', '{event_name}', '{event_organizer_name}',
            '{login_link}', '{register_link}', '{not_attending}', '{eventsite_URL}', '{badge_template}'
        );
        $template_array['attendee_reminder_email'] = array(
            '{event_logo}', '{attendee_name}', '{first_name}', '{last_name}', '{event_name}', '{event_organizer_name}',
            '{login_link}', '{not_attending}', '{badge_template}'
        );
        $template_array['attendee_invite_sms'] = array(
            '{attendee_name}', '{event_name}', '{login_link}',
            '{event_organizer_name}'
        );
        $template_array['attendee_reminder_sms'] = array(
            '{attendee_name}', '{event_name}', '{login_link}',
            '{event_organizer_name}'
        );
        $template_array['invite'] = array('{event_logo}', '{name}', '{email}', '{link}', '{app_store_icon}', '{google_play_icon}');
        $template_array['invite_sms'] = array('{first_name}', '{last_name}', '{email}', '{link}');

        $template_array['document'] = array('{event_logo}', '{sender_name}', '{sender_email}', '{link}');
        $template_array['share_document'] = array('{event_logo}', '{sender_name}', '{sender_email}', '{reciever_name}', '{reciever_email}', '{link}');

        $template_array['attendee'] = array(
            '{event_logo}', '{attendee_initial}', '{attendee_name}', '{initial}', '{first_name}', '{last_name}', '{gender}', '{event_name}',
            '{event_organizer_name}', '{attendee_email}', '{attendee_password}', '{attendee_image}',
            '{attendee_compnay_name}', '{attendee_department}', '{attendee_site_area}', '{title}', '{attendee_industry}',
            '{about}', '{website}', '{facebook}', '{twitter}', '{linkedin}', '{profile_link}', '{app_link}', '{qr_code}',
            '{app_store_icon}', '{google_play_icon}', '{not_attending}', '{attendee_groups}', '{badge_template}'
        );
        $template_array['attendee_sms'] = array(
            '{first_name}', '{last_name}', '{attendee_email}', '{attendee_password}',
            '{app_link}', '{event_name}', '{event_organizer_name}'
        );
        $template_array['sponsor'] = array(
            '{event_logo}', '{message}', '{sponsor_name}', '{sponsor_email}', '{sponsor_contact_person}',
            '{sponsor_logo}', '{sponsor_description}', '{sponsor_phone_number}', '{sponsor_website}', '{sponsor_facebook}',
            '{sponsor_twitter}', '{sponsor_linkedin}', '{event_organizer_name}'
        );
        $template_array['exhibitor'] = array(
            '{event_logo}', '{message}', '{exhibitor_name}', '{exhibitor_email}',
            '{exhibitor_contact_person}', '{exhibitor_logo}', '{exhibitor_description}', '{exhibitor_phone_number}',
            '{exhibitor_website}', '{exhibitor_facebook}', '{exhibitor_twitter}', '{exhibitor_linkedin}',
            '{event_organizer_name}'
        );

        $template_array['reset_password'] = array('{event_logo}', '{name}', '{email}', '{password}', '{event_organizer_name}');
        $template_array['native_app_reset_password'] = array('{event_logo}', '{attendee_name}', '{code}', '{event_name}');
        $template_array['speaker'] = array(
            '{event_logo}', '{message}', '{speaker_name}', '{speaker_email}', '{speaker_phone}',
            '{speaker_picture}', '{speaker_company_name}', '{speaker_title}', '{speaker_about}', '{speaker_website}',
            '{speaker_facebook}', '{speaker_twitter}', '{speaker_linkedin}', '{event_organizer_name}'
        );
        $template_array['attendee_profile_email'] = array(
            '{event_logo}', '{message}', '{attendee_initial}', '{attendee_name}',
            '{attendee_email}', '{attendee_age}', '{attendee_gender}', '{attendee_image}', '{attendee_compnay_name}',
            '{attendee_organization}', '{attendee_department}', '{attendee_site_area}', '{attendee_country}',
            '{attendee_job_tasks}', '{attendee_interests}', '{title}', '{attendee_industry}', '{about}', '{website}',
            '{phone_number}', '{facebook}', '{twitter}', '{linkedin}', '{profile_link}', '{event_organizer_name}'
        );
        $template_array['poll_results'] = array('{event_logo}', '{correct_numbers}', '{total_questions}');
        $template_array['qr_code'] = array(
            '{event_logo}', '{event_name}', '{event_date}', '{order_id}', '{attendee_name}', '{qr_code}',
            '{event_organizer_name}', '{email}', '{badge_template}'
        );
        $template_array['ticket_sponsor_exhibitor'] = array('{sponsor_exhibitor_name}', '{ticket_pdf_link}', '{event_name}', '{event_organizer_name}', '{event_logo}', '{ticket_order_number}', '{ticket_order_date}');

        $template_array['email_my_notes'] = array('{program_notes}', '{sponsor_notes}', '{exhibitor_notes}', '{documents_notes}', '{event_name}', '{event_organizer_name}', '{event_logo}', '{attendee_name}');

        $template_array['registration_verification'] = array('{event_logo}', '{event_name}', '{attendee_name}', '{initial}', '{first_name}', '{last_name}', '{gender}', '{event_url}', '{event_organizer_name}', '{app_link}', '{qr_code}', '{badge_template}', '{unsubscribe_attendee}');
        $template_array['mobile_app_link'] = array('{event_logo}', '{attendee_name}', '{app_link}', '{ios_app_link}', '{andriod_app_link}');
        $template_array['sub_reg_reminder_email'] = array('{event_logo}', '{attendee_name}', '{event_name}', '{event_organizer_name}', '{app_link}', '{badge_template}');
        $template_array['sub_reg_reminder_sms'] = array('{first_name}', '{app_link}', '{event_organizer_name}');

        $template_array['poll_reminder_email'] = array('{event_logo}', '{attendee_name}', '{first_name}', '{last_name}', '{event_name}', '{event_organizer_name}', '{app_link}');
        $template_array['poll_reminder_sms'] = array('{first_name}', '{app_link}');
        $template_array['survey_reminder_email'] = array('{event_logo}', '{attendee_name}', '{first_name}', '{last_name}', '{event_name}', '{event_organizer_name}', '{app_link}');
        $template_array['survey_reminder_sms'] = array('{first_name}', '{app_link}');
        $template_array['indentity_verification'] = array('{event_logo}', '{attendee_name}', '{initial}', '{first_name}', '{last_name}', '{gender}', '{link}', '{event_organizer_name}', '{event_name}');
        $template_array['attendee_cancel_registration'] = array('{event_logo}', '{attendee_name}', '{initial}', '{first_name}', '{last_name}', '{gender}', '{event_name}', '{comment}', '{event_organizer_name}');
        $template_array['checkin_attendee_invite'] = array('{event_logo}', '{attendee_name}', '{event_name}', '{event_organizer_name}', '{login_link}', '{qr_code}');
        $template_array['checkin_attendee_sms'] = array(
            '{attendee_name}', '{event_name}', '{event_organizer_name}',
            '{login_link}'
        );
        $template_array['qa'] = array('{event_logo}', '{attendee_name}', '{speaker_name}', '{email}', '{delegate_number}', '{company_name}', '{event_name}', '{qa}', '{program_name}, {event_organizer_name}');
        $template_array['qa_attendee_email'] = array('{event_logo}', '{attendee_name}', '{speaker_name}', '{email}', '{delegate_number}', '{company_name}', '{event_name}', '{qa}', '{program_name}, {event_organizer_name}');
        $template_array['sub_registration_result_email'] = array('{event_logo}', '{event_name}', '{attendee_name}', '{first_name}', '{last_name}', '{sub_registration_result_detail}', '{event_organizer_name}');
        $template_array['sub_registration_update_result_email'] = array('{event_logo}', '{event_name}', '{attendee_name}', '{first_name}', '{last_name}', '{result_detail}', '{event_organizer_name}');


        $template_array['reservation_new_request_email'] = array('{event_logo}', '{attendee_name}', '{event_name}', '{module_name}', '{event_organizer_name}', '{date}', '{time}', '{contact_person_name}', '{sponsor_name}');
        $template_array['reservation_reject_email'] = array('{event_logo}', '{attendee_name}', '{event_name}', '{module_name}', '{event_organizer_name}', '{date}', '{time}', '{contact_person_name}', '{sponsor_name}');
        $template_array['reservation_accept_email'] = array('{event_logo}', '{attendee_name}', '{event_name}', '{module_name}', '{event_organizer_name}', '{date}', '{time}', '{contact_person_name}', '{sponsor_name}');
        $template_array['reservation_cancel_email'] = array('{event_logo}', '{attendee_name}', '{event_name}', '{module_name}', '{event_organizer_name}', '{date}', '{time}', '{contact_person_name}', '{sponsor_name}', '{attendee_cancellation_comment}');
        $template_array['reservation_request_email'] = array('{event_logo}', '{attendee_name}', '{event_name}', '{module_name}', '{event_organizer_name}', '{date}', '{time}', '{contact_person_name}', '{sponsor_name}');
        $template_array['contact_person_reservation_cancel_email'] = array('{event_logo}', '{attendee_name}', '{event_name}', '{module_name}', '{event_organizer_name}', '{date}', '{time}', '{contact_person_name}', '{sponsor_name}');

        $template_array['reservation_new_request_sms'] = array('{event_logo}', '{attendee_name}', '{event_name}', '{module_name}', '{event_organizer_name}', '{date}', '{time}', '{contact_person_name}', '{sponsor_name}');
        $template_array['reservation_reject_sms'] = array('{event_logo}', '{attendee_name}', '{event_name}', '{module_name}', '{event_organizer_name}', '{date}', '{time}', '{contact_person_name}', '{sponsor_name}');
        $template_array['reservation_accept_sms'] = array('{event_logo}', '{attendee_name}', '{event_name}', '{module_name}', '{event_organizer_name}', '{date}', '{time}', '{contact_person_name}', '{sponsor_name}');
        $template_array['reservation_cancel_sms'] = array('{event_logo}', '{attendee_name}', '{event_name}', '{module_name}', '{event_organizer_name}', '{date}', '{time}', '{contact_person_name}', '{sponsor_name}');
        $template_array['reservation_request_sms'] = array('{event_logo}', '{attendee_name}', '{event_name}', '{module_name}', '{event_organizer_name}', '{date}', '{time}', '{contact_person_name}', '{sponsor_name}');
        $template_array['attendee_free_registration'] = array('{event_logo}', '{attendee_name}', '{first_name}', '{last_name}', '{event_name}', '{event_organizer_name}', '{qr_code}', '{badge_template}', '{not_attending}', '{unsubscribe_attendee}');
        $template_array['invoice_reminder_email'] = array('{event_logo}', '{attendee_name}', '{message}', '{attendee_email}', '{invoice_number}', '{event_name}', '{event_organizer_name}', '{invoice_date}');
        $template_array['contact_person_reservation_cancel_sms'] = array('{event_logo}', '{attendee_name}', '{event_name}', '{module_name}', '{event_organizer_name}', '{date}', '{time}', '{contact_person_name}', '{sponsor_name}');
        $template_array['waiting_list_registration_invite'] = array('{event_logo}', '{attendee_name}', '{event_name}', '{event_organizer_name}', '{accept_link}', '{cancel_link}');
        $template_array['waiting_list_registration_confirmation'] = array('{event_logo}', '{attendee_name}', '{event_name}', '{event_organizer_name}', '{cancel_link}');
        $template_array['mailing_list'] = array('{rss_items}', '{unsubscribe}');
        $template_array['invitees_who_abandoned_registration'] = array('{event_logo}', '{attendee_name}', '{first_name}', '{last_name}', '{event_name}', '{event_organizer_name}');
        $template_array['native_app_reset_password_sms'] = array('{event_name}');

        if ($all) {
            return $template_array;
        } else {
            return $template_array[$alias];
        }
    }

    /**
     * Set form values for creation/updation
     *
     * @param array
     * @param string
     * @param string
     */

    public function setForm($formInput)
    {
        $formInput['event_id'] = $formInput['event_id'];

        $this->setFormInput($formInput);

        return $this;
    }

    /**
     * update data for template
     *
     * @param array
     * @param int
     */

    public function edit($formInput, $id)
    {

        $instance = $this->setForm($formInput);

        return $instance->updateInfo($id);

    }

    /**
     * update info for template
     *
     * @param int
     */
    public function updateInfo($id)
    {
        $formInput = $this->getFormInput();

        //Templates Log
        $information = \App\Models\EventEmailTemplate::find($id);

        $log = new \App\Models\EventEmailTemplateLog();

        if(isset($formInput['is_new_flow']) && $formInput['is_new_flow'] && in_array($information->alias, ['registration_invite', 'registration_verification', 'attendee_cancel_registration', 'waiting_list_registration_invite', 'attendee_reminder_email', 'waiting_list_registration_confirmation'])) {

            $information = \App\Models\RegistrationFormTemplate::where('event_id', $formInput['event_id'])
                                    ->where('alias', $information->alias)
                                    ->where('registration_form_id', (int)$formInput['registration_form_id'])
                                    ->where('type', 'email')
                                    ->first();

            if($information) {

                $log->template_id = $information->id;
    
                $log->title = $information->title;
    
                $log->template = $information->template;
    
                $log->subject = $information->subject;
    
                $log->save();

                $info_fields = (isset($formInput['type']) && $formInput['type'] == "old" ? array('subject', 'template') : array('subject'));

                foreach ($info_fields as $field) {
    
                    if ($field == 'template') {
                        $head = '';
                        $formInput[$field] = preg_replace('@<style id="background_color" type="text/css">.*?</style>@siu', $head, $formInput[$field]);
                    }

                    $information->{$field} = $formInput[$field];
                }
                
                $information->save();
            }
            
        } else {
            
            $information = $information->info()->where('template_id', $id)->where('languages_id', $formInput['language_id'])->get();

            $log->template_id = $id;

            foreach ($information as $info) {
    
                $log->status = $info->status;
    
                $log->languages_id = $info->languages_id;
    
                if (in_array($info->name, ['title', 'template', 'subject'])) {
                    if ($info->name == "title") $log->title =  ($info->value ? $info->value : '');
                    if ($info->name == "template") $log->template =  ($info->value ? $info->value : '');
                    if ($info->name == "subject") $log->subject =  ($info->value ? $info->value : '');
                }
    
            }
    
            $log->save();
            //Templates Log End
    
            $info_fields = (isset($formInput['type']) && $formInput['type'] == "old" ? array('subject', 'template') : array('subject'));
    
            foreach ($info_fields as $field) {
    
                if ($field == 'template') {
                    $head = '';
                    $formInput[$field] = preg_replace('@<style id="background_color" type="text/css">.*?</style>@siu', $head, $formInput[$field]);
                }
    
                \App\Models\EmailTemplateInfo::where('template_id', $id)->where('languages_id', $formInput['language_id'])
                    ->where('name', '=', $field)
                    ->update(array('value' => $formInput[$field]));
    
            }
        }

        return $this;
    }

    /**
     * templates log history
     *
     * @param array
     */
    public function logs($formInput)
    {
        $result = \App\Models\EventEmailTemplateLog::where('template_id', '=', $formInput['template_id'])
            ->where('languages_id', '=', $formInput['language_id'])
            ->orderBy('id', 'ASC');
        return $result->paginate($formInput['limit'])->toArray();
    }

    /**
     * view template log history
     *
     * @param array
     */
    public function view_history($formInput)
    {
        $result = \App\Models\EventEmailTemplateLog::where('id', '=', $formInput['id'])
            ->where('languages_id', '=', $formInput['language_id']);

        $history = $result->first();

        $template = \App\Models\EventEmailTemplate::where('id', $history->template_id)->first();

        if ($template) {
            $history->alias = $template->alias;
        }

        $style = email_background_color($formInput['event_id']);

        $history->style = (isset($style['css']) ? $style['css'] : '');

        return $history;
    }
    
    /**
     * getRegistrationFormTemplateData
     *
     * @param  mixed $formInput
     * @return void
     */
    public static function getRegistrationFormTemplateData($formInput) {

        $template = \App\Models\RegistrationFormTemplate::where('event_id', $formInput['event_id'])
            ->where('alias', 'registration_verification')
            ->where('registration_form_id', $formInput['registration_form_id'])
            ->where('type', 'email')
            ->first();

        if(!$template) {

            $template = new \App\Models\RegistrationFormTemplate();

            $email_template = \App\Models\EventEmailTemplate::where('event_id', $formInput['event_id'])
			->where('alias', '=', 'registration_verification')->where('type', '=', 'email')
			->with(['info' => function ($q) use ($formInput) {
				$q->where('languages_id', $formInput['language_id']);
			}])->first();

            if (isset($email_template['info'])) {
                foreach ($email_template['info'] as $row) {
                    if ($row['name'] == 'template') {
                        $template->template = $row['value'];
                    }
                    if ($row['name'] == 'subject') {
                        $template->subject = $row['value'];
                    }
                }

            }

        }

        return $template;
    }

    /**
     * getTemplateDataByAlias
     *
     * @param  mixed $formInput
     * @return void
     */
    public static function getTemplateDataByAlias($formInput) {

        $template = \App\Models\RegistrationFormTemplate::where('event_id', $formInput['event_id'])
            ->where('alias', $formInput['alias'])
            ->where('registration_form_id', $formInput['registration_form_id'])
            ->where('type', 'email')
            ->first();

        if(!$template) {

            $template = new \App\Models\RegistrationFormTemplate();

            $email_template = \App\Models\EventEmailTemplate::where('event_id', $formInput['event_id'])
			->where('alias', '=', $formInput['alias'])->where('type', '=', 'email')
			->with(['info' => function ($q) use ($formInput) {
				$q->where('languages_id', $formInput['language_id']);
			}])->first();

            if (isset($email_template['info'])) {
                foreach ($email_template['info'] as $row) {
                    if ($row['name'] == 'template') {
                        $template->template = $row['value'];
                    }
                    if ($row['name'] == 'subject') {
                        $template->subject = $row['value'];
                    }
                }

            }

        }

        return $template;
    }
}
