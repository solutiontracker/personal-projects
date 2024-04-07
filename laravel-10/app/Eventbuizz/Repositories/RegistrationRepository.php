<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;
use App\Models\BillingField;

class RegistrationRepository extends AbstractRepository
{
    protected $sectionAlias = ['basic', 'company_detail', 'attendee_type_head'];
    protected $basicRequiredField = ['first_name', 'email'];
    protected $companyDetailNotIncludeInFreeEvent = ['company_registration_number', 'company_type', 'ean', 'credit_card_payment', 'company_public_payment', 'company_invoice_payment'];
    public function __construct(Request $request, BillingField $model)
    {
        $this->formInput = $request;
        $this->model = $model;
    }

    /*
     * param: $section_alias
     * return: section fields
     */
    public function getRegistrationFields($alias = '')
    {
        $formInput = $this->getFormInput();
        $eventsite_setting = eventsite_setting($formInput['event_id']);
        $fields = [];
        if (!empty($alias) && in_array($alias, $this->sectionAlias)) {
            if ($alias == 'company_detail' && isset($eventsite_setting['payment_type']) && $eventsite_setting['payment_type'] == 0) {
                $sectionFields = \App\Models\BillingField::where('event_id', $formInput['event_id'])
                    ->where('type', 'field')->where('section_alias', $alias)
                    ->whereNotIn('field_alias', $this->companyDetailNotIncludeInFreeEvent)->with(['info' => function ($query) use ($formInput) {
                        return $query->where('languages_id', $formInput['language_id']);
                    }])->orderBy('sort_order', 'asc')->get();
            } else {
                $sectionFields = \App\Models\BillingField::where('event_id', $formInput['event_id'])
                    ->where('type', 'field')->where('section_alias', $alias)->with(['info' => function ($query) use ($formInput) {
                        return $query->where('languages_id', $formInput['language_id']);
                    }])->orderBy('sort_order', 'asc')->get();
            }

            foreach ($sectionFields as $index => $field) {
                if (isset($field->info[0])) {
                    $always = false;
                    if ($alias == 'basic' && in_array($field->field_alias, $this->basicRequiredField))
                        $always = true;
                    $fields[] = [
                        'id' => $field->id,
                        'field_alias' => $field->field_alias,
                        'status' => $always ? 1 : $field->status,
                        'mandatory' => $always ? 1 : $field->mandatory,
                        'name' => ucfirst(strtolower($field->info[0]->value)),
                        'non_editable' => $always ? 1 : 0,
                        'sort_order' => $field->sort_order

                    ];
                }
            }
            return $fields;
        }

        return $fields;
    }

    public function updateRegistrationFields($alias = '')
    {
        $formInput = $this->getFormInput();
        if (!empty($alias) && in_array($alias, $this->sectionAlias)) {
            $fields = $formInput['data'];
            $activeFieldIds = [];
            foreach ($fields as $key => $field) {
                $billingField = \App\Models\BillingField::where('id', $field['backend_id'])->where('type', 'field')->where('field_alias', $field['alias'])
                    ->with(['info' => function($q) use ($formInput){
                        $q->where('languages_id', $formInput['language_id']);
                    }])
                    ->first();
                if ($billingField) {
                    $activeFieldIds[] = $field['backend_id'];
                    $billingField->sort_order = ++$key;
                    $billingField->mandatory = $field['required'];
                    $billingField->status = 1;
                    $billingField->save();
                    $billingField->info[0]->value = $field['name'];
                    if((isset($formInput['alias']) && $formInput['alias'] == $field['alias']) || !isset($formInput['alias'])) {
                        $billingField->info[0]->save();
                    }
                }
            }
            \App\Models\BillingField::whereNotIn('id', $activeFieldIds)
                ->where('event_id', $formInput['event_id'])
                ->where('type', 'field')->where('section_alias', $alias)
                ->update(['status' => 0, 'mandatory' => 0, 'sort_order' => (count($activeFieldIds) + 1)]);
        }
    }
}
