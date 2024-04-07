<?php

namespace App\Http\Controllers\Wizard\Requests\Alert;

use Illuminate\Foundation\Http\FormRequest;

class AlertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (!request()->isMethod('GET')) {
            return [
                'title' => 'required',
                'alert_date' => 'required_if:pre_schedule,1',
                'alert_time' => 'required_if:pre_schedule,1',
                'group_id' => 'required_if:sendto,groups',
                'program_id' => 'required_if:sendto,agendas',
                'individual_id' => 'required_if:sendto,individuals',
                'sponsor_id' => 'required_if:sendto,sponsor',
                'exhibitor_id' => 'required_if:sendto,exhibitor',
                'workshop_id' => 'required_if:sendto,workshops'
            ];
        } else {
             return [];
        }
    }

    public function messages()
    {
        return [
            'alert_date.required_if' => 'The alert date field is required when pre schedule is checked',
            'alert_time.required_if' => 'The alert time field is required when pre schedule is checked',
        ];
    }
}
