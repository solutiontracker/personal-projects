<?php

namespace App\Http\Controllers\Auth\Attendee\Requests;

use App\Http\Requests\Api\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerificationRequest extends FormRequest
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
        if ($this->request->get('screen') == "verification" && request()->isMethod('POST')) {
            return [
                'code' => 'required',
            ];
        } else if ($this->request->get('screen') == "choose-provider" && request()->isMethod('POST')) {
            return [
                'provider' => 'bail|required',
            ];
        } else {
            return [];
        }
    }

    /**
     * @return [type]
     */
    public function messages()
    {
        $event = request()->event;
        $label = $event['labels'];

        return [
            'provider.required' => $label['EVENTSITE_AUTHENTICATION_CONTACT_METHOD'],
            'code.required' => $label['EVENTSITE_AUTHENTICATION_CODE_REQUIRED'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        if ($this->request->get('screen') == "verification" && request()->isMethod('POST')) {
            $event = request()->event;
            $label = $event['labels'];
            $authentication = \App\Models\AttendeeAuthentication::where('id', $this->request->get('authentication_id'))->where('event_id', $event['id'])->first();
            if ($authentication) {
                $start = \Carbon\Carbon::now();
                $end = new \Carbon\Carbon($authentication->expire_at);
                $seconds = $start->diffInSeconds($end);
                if ($start->lessThan($end) && $authentication) {
                    $timer = gmdate('i:s', $seconds);
                } else {
                    $timer = 0;
                }

                if (request()->code != $authentication->token) {
                    $validator->after(function ($validator) use ($label) {
                        $validator->errors()->add('code', $label['EVENTSITE_INVALID_AUTHENTICATION_CODE']);
                    });
                } else if ($timer == 0) {
                    $validator->after(function ($validator) use ($label) {
                        $validator->errors()->add('code', $label['EVENTSITE_AUTHENTICATION_TIME_EXPIRED']);
                    });
                }
            } else {
                $validator->after(function ($validator) use ($label) {
                    $validator->errors()->add('code', $label['EVENTSITE_AUTHENTICATION_TIME_EXPIRED']);
                });
            }
        }
    }

    /**
     *  update response
     *
     * @param object
     */
    public function failedValidation(Validator $validator)
    {
        $event = request()->event;
        $authentication = \App\Models\AttendeeAuthentication::where('id', $this->request->get('authentication_id'))->where('event_id', $event['id'])->first();
        if ($authentication) {
            $start = \Carbon\Carbon::now();
            $end = new \Carbon\Carbon($authentication->expire_at);
            $seconds = $start->diffInSeconds($end);
            if ($start->lessThan($end) && $authentication) {
                $seconds = ($seconds > 0 ? $seconds * 1000 : 0);
            } else {
                $seconds = 0;
            }
            throw new HttpResponseException(response()->json(array(
                "success" => false,
                "message" => set_error_delimeter($validator->errors()->all()),
                'data' => array(
                    'ms' => $seconds,
                ),
            ), 422));
        } else {
            throw new HttpResponseException(response()->json(array(
                "success" => false,
                "message" => set_error_delimeter($validator->errors()->all()),
            ), 422));
        }

    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'authentication_id' => request()->id,
        ]);
    }
}
