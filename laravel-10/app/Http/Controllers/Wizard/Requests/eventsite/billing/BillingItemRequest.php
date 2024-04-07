<?php

namespace App\Http\Controllers\Wizard\Requests\eventsite\billing;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Support\Arr;

use App\Eventbuizz\Repositories\EventSiteSettingRepository;

use App\Eventbuizz\Repositories\EventsiteBillingItemRepository;

class BillingItemRequest extends FormRequest
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
        $payment_setting = EventSiteSettingRepository::getSetting($this->request->all());
        if (request()->isMethod('PUT') && (\Route::is('wizard-eventsite-billing-item-edit') || \Route::is('wizard-eventsite-billing-item-create')) && request()->type == "item") {
            return [
                'link_to' => 'bail|required',
                'item_name' => 'bail|required',
                'item_number' => (request()->item_number ? 'bail|required|max:6|min:1' : ""),
                'link_to_id' => (request()->link_to = !"none" ? 'bail|not_in:0' : ''),
                'price' => ($payment_setting->payment_type == 1 ? 'bail|required|numeric' : ""),
                'vat' => (request()->vat ? 'bail|required|numeric' : ""),
                'total_tickets' => (request()->total_tickets ? 'bail|required|numeric' : ""),
                'qty' => (request()->qty ? 'bail|required|integer' : "")
            ];
        } else if (request()->isMethod('PUT') && (\Route::is('wizard-eventsite-billing-item-edit') || \Route::is('wizard-eventsite-billing-item-create')) && request()->type == "group") {
            return [
                'group_name' => 'bail|required'
            ];
        } else {
            return [];
        }
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        if (request()->isMethod('PUT') && (\Route::is('wizard-eventsite-billing-item-edit') || \Route::is('wizard-eventsite-billing-item-create')) && request()->type == "item") {
            if ($this->validateLinkTo(request()->all())) {
                $validator->after(function ($validator) {
                    $validator->errors()->add('link_to', 'Order is placed with this item so you can,t change "Link to" field.');
                });
            } else if ($this->checkEndDate()) {
                $validator->after(function ($validator) {
                    $validator->errors()->add('date_prices', 'End date must be equal or greater than start date.');
                });
            } else if ($this->validateDateRules()) {
                $validator->after(function ($validator) {
                    $validator->errors()->add('date_prices', 'Some dates are overlapping. Which is not allowed.');
                });
            } else if ($this->uniqueness()) {
                $validator->after(function ($validator) {
                    $validator->errors()->add('link_to', 'Item with link to already exists.');
                });
            }
        }
    }

    public function validateLinkTo($formInput)
    {
        if (request()->isMethod('PUT') && \Route::is('wizard-eventsite-billing-item-edit')) {
            $parameters = \Route::current()->parameters();
            $item = \App\Models\BillingItem::where("id", $parameters["id"])->first();
            $soldTickets = EventsiteBillingItemRepository::getItemSoldTickets($parameters["id"]);
            if ($item->link_to != $formInput["link_to"] && $soldTickets > 0) {
                return true;
            }
        }
        return false;
    }

    public function validateDateRules()
    {
        if (request()->date_prices) {
            $ranges =  request()->date_prices;
            foreach ($ranges as $i => $parent) {
                $parent_start_date = strtotime(Arr::first($parent['value']));
                $parent_end_date = strtotime(Arr::last($parent['value']));
                foreach ($ranges as $j => $child) {
                    if ($j == $i) {
                        continue;
                    }
                    $child_start_date = strtotime(Arr::first($child['value']));
                    $child_end_date = strtotime(Arr::last($child['value']));
                    if (Arr::last($parent['value']) == '') {
                        if ($parent_start_date <= $child_start_date) {
                            return true;
                        }
                    } elseif (Arr::last($child['value']) == '') {
                        if ($parent_start_date > $child_start_date) {
                            return true;
                        }
                    } elseif (Arr::last($child['value']) == '' && Arr::last($parent['value']) == '') {
                        return true;
                    } else {
                        if ($parent_start_date >= $child_start_date && $parent_start_date <= $child_end_date || $parent_end_date >= $child_start_date && $parent_end_date <= $child_end_date || $child_start_date >= $parent_start_date && $child_start_date <= $parent_end_date || $child_end_date >= $parent_start_date && $child_end_date <= $parent_end_date) {
                            return true;
                        }
                    }
                }
            }
            return false;
        }
        return false;
    }

    public function checkEndDate()
    {
        if (request()->date_prices) {
            $ranges =  request()->date_prices;
            foreach ($ranges as $date) {
                $start_date = strtotime(Arr::first($date['value']));;
                $end_date = strtotime(Arr::last($date['value']));
                if ($start_date > $end_date && trim($date['end_date']) != '' && trim($date['end_date']) != '00-00-0000') {
                    return true;
                }
            }
        }
        return false;
    }

    public function uniqueness()
    {
        $link_to_id = (is_array(request()->link_to_id) ? implode(",", request()->link_to_id ?? []) : request()->link_to_id);
        
        if ($link_to_id) {
            $link_to     = request()->link_to;

            /* if link to program/attendee_group then no need to check program/attendee_group already in used or not */
            if (in_array($link_to, ["attendee_group"])) {
                return false;
            }

            $query = \App\Models\BillingItem::where('organizer_id', organizer_id())->where('link_to', strtolower($link_to))->where('link_to_id', $link_to_id)->where('is_archive', '0');

            if (request()->isMethod('PUT') && \Route::is('wizard-eventsite-billing-item-edit')) {
                $query->where('id', '<>', request()->id);
            }

            $count = $query->count();

            if ($count > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
