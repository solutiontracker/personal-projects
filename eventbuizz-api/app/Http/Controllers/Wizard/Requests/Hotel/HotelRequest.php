<?php

namespace App\Http\Controllers\Wizard\Requests\Hotel;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class HotelRequest extends FormRequest
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
        return [
            'name' => 'bail|required',
            'price' => 'bail|required|numeric',
            'from_date' => 'bail|required',
            'to_date' => 'bail|required',
            'room_range' => 'bail|required'
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'from_date' => (request()->from_date ? \Carbon\Carbon::parse(request()->from_date)->toDateString() : ''),
            'to_date' => (request()->to_date ? \Carbon\Carbon::parse(request()->to_date)->toDateString() : ''),
            'hotel_from_date' => (request()->from_date ? \Carbon\Carbon::parse(request()->from_date)->toDateString() : ''),
            'hotel_to_date' => (request()->to_date ? \Carbon\Carbon::parse(request()->to_date)->toDateString() : ''),
        ]);
    }

    public function withValidator($validator)
    {
        $room_range = false;
        if ($this->request->has('room_range') && !empty($this->request->get('room_range'))) {
            $periods = days(\Carbon\Carbon::parse(request()->from_date)->toDateString(), \Carbon\Carbon::parse(request()->to_date)->toDateString());
            $room_range_arr = json_decode($this->request->get('room_range'), true);
            if(count($room_range_arr) == $periods)
                $room_range = true;
        }

        $validator->after(function ($validator) use ($room_range) {
            if (!$room_range) $validator->errors()->add('room_range', __('messages.room_range'));
        });
    }
}