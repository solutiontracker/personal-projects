<?php

namespace App\Eventbuizz\Repositories;

use \App\Models\EventAlert;

class AlertRepository extends AbstractRepository
{
    protected $model;

    public function __construct(EventAlert $model)
    {
        $this->model = $model;
    }

    public function listing($formInput)
    {
        $records = \App\Models\EventAlert::where('event_id', $formInput['event_id'])->with(['info' => function ($query) use ($formInput) {
            return $query->where('languages_id', $formInput['language_id']);
        }])->paginate($formInput['limit'])->toArray();

        foreach ($records['data'] as $key => $row) {
            $response = array();
            $info = readArrayKey($row, $response, 'info');
            $row['info'] = $info;
            $records['data'][$key] = $row;
            $records['data'][$key]['display_alert_date'] = \Carbon\Carbon::parse($row['alert_date'])->format('d/m/y');
        }

        return $records;
    }

    public function getAllAlerts($formInput)
    {
         return \App\Models\EventAlert::where('event_id', $formInput['event_id'])->with(['info' => function ($query) use ($formInput) {
            return $query->where('languages_id', $formInput['language_id']);
         }])->get();
    }

    public function setForm($formInput)
    {
        if (!isset($formInput['alert_email'])) {
            $formInput['alert_email'] = '0';
        }
        if (!isset($formInput['alert_sms'])) {
            $formInput['alert_sms'] = '0';
        }
        $formInput['status'] = '1';
        if ($formInput['pre_schedule'] == 0) {
            $timezone =  \App\Models\Timezone::join('conf_events', function ($join) use ($formInput) {
                $join->on('conf_timezones.id', '=', 'conf_events.timezone_id')
                    ->where('conf_events.id', '=', $formInput['event_id']);
            })->value('conf_timezones.timezone');

            $formInput['alert_date'] = \Carbon\Carbon::now($timezone)->toDateString();
            $formInput['alert_time'] = \Carbon\Carbon::now($timezone)->toTimeString();
        } else {
            $formInput['pre_schedule'] = '1';
            $formInput['alert_date'] = \Carbon\Carbon::parse($formInput['alert_date'])->toDateString();
            $formInput['alert_time'] = \Carbon\Carbon::parse($formInput['alert_time'])->toTimeString();
        }

        $this->setFormInput($formInput);
        return $this;
    }

    public function store($formInput)
    {
        $formInput = get_trim_all_data($formInput);
        $instance = $this->setForm($formInput);
        $instance->create();
        $instance->insertInfo();
        $instance->insertRelatedData();
    }

    public function edit($formInput, $object)
    {
        $formInput = get_trim_all_data($formInput);
        $instance = $this->setForm($formInput);
        $instance->update($object);
        $instance->updateInfo();
        $instance->removeRelatedData();
        $instance->insertRelatedData();
    }

    public function insertInfo()
    {
        $formInput = $this->getFormInput();
        $languages = get_event_languages($formInput['event_id']);
        $info_fields = array('title', 'description');
        $alert = $this->getObject();
        $model = \App\Models\EventAlert::find($alert->id);

        foreach ($info_fields as $field) {
            foreach ($languages as $lang) {
                $alert_info_model_array[] = new \App\Models\EventAlertInfo(array(
                    'name' => $field,
                    'value' => trim($formInput[$field]), 'alert_id' => $alert->id, 'languages_id' => $lang,
                    'status' => 1
                ));
            }
        }
        $model->info()->saveMany($alert_info_model_array);
        return $this;
    }

    public function insertRelatedData()
    {
        $formInput = $this->getFormInput();
        $alert = $this->getObject();
        if ($formInput['sendto'] == 'groups') {
            foreach ($formInput['group_id'] as $group) {
                if ($group) {
                    $group_alert = new \App\Models\EventAlertGroup();
                    $group_alert->group_id = $group['value'];
                    $group_alert->alert_id = $alert->id;
                    $group_alert->status = '0';
                    $group_alert->save();
                }
            }
        }
        if ($formInput['sendto'] == 'agendas') {
            foreach ($formInput['programs'] as $program) {
                if ($program) {
                    $program_alert = new \App\Models\EventAlertAgenda();
                    $program_alert->agenda_id = $program;
                    $program_alert->alert_id = $alert->id;
                    $program_alert->status = '0';
                    $program_alert->save();
                }
            }
        }
        if ($formInput['sendto'] == 'individuals') {
            foreach ($formInput['individual_id'] as $individual) {
                if ($individual) {
                    $individual_alert = new \App\Models\EventAlertIndividual();
                    $individual_alert->attendee_id = $individual['value'];
                    $individual_alert->alert_id = $alert->id;
                    $individual_alert->status = '0';
                    $individual_alert->save();
                }
            }
        }
        if ($formInput['sendto'] == 'workshops') {
            $workshop_alert = new \App\Models\EventWorkshopAlert();
            $workshop_alert->workshop_id = $formInput['workshop'];
            $workshop_alert->alert_id = $alert->id;
            $workshop_alert->status = '0';
            $workshop_alert->save();
        }
        if ($formInput['sendto'] == 'polls') {
            $workshop_alert = new \App\Models\EventPollAlert();
            $workshop_alert->poll_id = $formInput['polls'];
            $workshop_alert->alert_id = $alert->id;
            $workshop_alert->status = '0';
            $workshop_alert->save();
        }
        if ($formInput['sendto'] == 'surveys') {
            $workshop_alert = new \App\Models\EventSurveyAlert();
            $workshop_alert->survey_id = $formInput['surveys'];
            $workshop_alert->alert_id = $alert->id;
            $workshop_alert->status = '0';
            $workshop_alert->save();
        }
        if ($formInput['sendto'] == 'sponsors') {
            $sponsor_alert = new \App\Models\EventSponsorAlert();
            $sponsor_alert->sponsor_id = $formInput['sponsors'];
            $sponsor_alert->alert_id = $alert->id;
            $sponsor_alert->status = '0';
            $sponsor_alert->save();
        }
        if ($formInput['sendto'] == 'exhibitors') {
            $exhibitor_alert = new \App\Models\EventExhibitorAlert();
            $exhibitor_alert->exhibitor_id = $formInput['exhibitors'];
            $exhibitor_alert->alert_id = $alert->id;
            $exhibitor_alert->status = '0';
            $exhibitor_alert->save();
        }
        if ($formInput['sendto'] == 'attendee_type') {
            foreach ($formInput['attendee_type_id'] as $attendee_type_id) {
                if($attendee_type_id) {
                    $group_alert = new \App\Models\EventAlertAttendeeType();
                    $group_alert->attendee_type_id = $attendee_type_id['value'];
                    $group_alert->alert_id = $alert->id;
                    $group_alert->status = '0';
                    $group_alert->save();
                }
            }
        }
        return $this;
    }

    public function removeRelatedData()
    {
        $alert = $this->getObject();
        $id = $alert->id;
        \App\Models\EventAlertGroup::where('alert_id', '=', $id)->delete();
        \App\Models\EventAlertAgenda::where('alert_id', '=', $id)->delete();
        \App\Models\EventAlertIndividual::where('alert_id', '=', $id)->delete();
        \App\Models\EventWorkshopAlert::where('alert_id', '=', $id)->delete();
        \App\Models\EventPollAlert::where('alert_id', '=', $id)->delete();
        \App\Models\EventSurveyAlert::where('alert_id', '=', $id)->delete();
        \App\Models\EventSponsorAlert::where('alert_id', '=', $id)->delete();
        \App\Models\EventExhibitorAlert::where('alert_id', '=', $id)->delete();
        \App\Models\EventAlertAttendeeType::where('alert_id', '=', $id)->delete();
        return $this;
    }

    public function updateInfo()
    {
        $alert = $this->getObject();
        $id = $alert->id;
        $formInput = $this->getFormInput();
        $info_fields = array('title', 'description');
        foreach ($info_fields as $field) {
            \App\Models\EventAlertInfo::where('alert_id', '=', $id)->where('languages_id', $formInput['language_id'])
                ->where('name', '=', $field)->update(array('value' => trim($formInput[$field])));
        }
        return $this;
    }

    public function destroy($id)
    {
        \App\Models\EventAlert::where('id', '=', $id)->delete($id);
        \App\Models\EventAlertInfo::where('alert_id', '=', $id)->delete();
        \App\Models\EventAlertGroup::where('alert_id', '=', $id)->delete();
        \App\Models\EventAlertAgenda::where('alert_id', '=', $id)->delete();
        \App\Models\EventAlertIndividual::where('alert_id', '=', $id)->delete();
        \App\Models\EventWorkshopAlert::where('alert_id', '=', $id)->delete();
        \App\Models\EventPollAlert::where('alert_id', '=', $id)->delete();
        \App\Models\EventSurveyAlert::where('alert_id', '=', $id)->delete();
        \App\Models\EventSponsorAlert::where('alert_id', '=', $id)->delete();
        \App\Models\EventExhibitorAlert::where('alert_id', '=', $id)->delete();
        \App\Models\EventAlertAttendeeType::where('alert_id', '=', $id)->delete();
    }
}
