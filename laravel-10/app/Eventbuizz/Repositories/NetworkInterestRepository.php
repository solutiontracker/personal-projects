<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;

class NetworkInterestRepository extends AbstractRepository
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * content copy / when new event create / cloning event
     *
     * @param array
     */
    public function install($request)
    {
        //Keywords
		$keywords = \App\Models\MatchMaking::where('event_id', $request['from_event_id'])
        ->where('parent_id', 0)
        ->get();

        foreach($keywords as $keyword) {
            $to_keyword = $keyword->replicate();
            $to_keyword->event_id = $request['to_event_id'];
            if (session()->has('clone.event.event_registration_form.' . $keyword->registration_form_id) && $keyword->registration_form_id > 0) {
                $to_keyword->registration_form_id = session()->get('clone.event.event_registration_form.' . $keyword->registration_form_id);
            }
            $to_keyword->save();

            $child_keywords = \App\Models\MatchMaking::where('event_id', $request['from_event_id'])
			->where('parent_id', $keyword->id)
			->get();

            foreach($child_keywords as $child_keyword) {
                $to_child_keyword = $child_keyword->replicate();
                $to_child_keyword->event_id = $request['to_event_id'];
                $to_child_keyword->parent_id = $to_keyword->id;
                if (session()->has('clone.event.event_registration_form.' . $child_keyword->registration_form_id) && $child_keyword->registration_form_id > 0) {
                    $to_child_keyword->registration_form_id = session()->get('clone.event.event_registration_form.' . $child_keyword->registration_form_id);
                }
                $to_child_keyword->save();
            }
        }
    }
    
    /**
     * @param mixed $formInput
     *
     * @return [type]
     */
    public function getAllKeywords($formInput)
    {
        $keywords = array();

        $query = \App\Models\MatchMaking::where('event_id', $formInput['event_id'])
            ->where('parent_id', '=', '0')
            ->where('registration_form_id', (int)$formInput['registration_form_id'])
            ->with(['keywords' => function ($q) use ($formInput) {
            return $q->where('attendee_id', $formInput['attendee_id']);
        }]);
        
        $parents = $query->orderBy('sort_order', 'ASC')
        ->get()
        ->toArray();

        foreach ($parents as $parent) {
            
            $query = \App\Models\MatchMaking::where('parent_id', '=', $parent['id'])
            ->where('registration_form_id', (int)$formInput['registration_form_id'])
            ->with(['keywords' => function ($q) use ($formInput) {
                return $q->where('attendee_id', $formInput['attendee_id']);
            }]);
            
            if(isset($formInput['filter']) && is_array($formInput['filter']) && !in_array(0, $formInput['filter'])) {
                $query->whereIn('parent_id', $formInput['filter']);
            }

            if(isset($formInput['search']) && $formInput['search']) {
                $query->where(\DB::raw('LOWER(name)'), 'LIKE', '%' . strtolower($formInput['search']) . '%');
            }

            $children = $query->orderBy('sort_order', 'ASC')->get()->toArray();

            $parent['children'] = array();

            foreach ($children as $child) {
                //Order
                $keyword = \App\Models\EventOrderKeyword::where('order_id', $formInput['order_id'])->where('attendee_id', $formInput['attendee_id'])->where('keyword_id', $child['id'])->first();
                if($keyword) {
                    $child['status'] = 1;
                }
                //End
                
                $parent['children'][] = $child;
            }
            $keywords[] = $parent;
        }

        return $keywords;
    }
    
    /**
     * saveOrderKeywords
     *
     * @param  mixed $formInput
     * @return void
     */
    public function saveOrderKeywords($formInput)
    {
        //First clean 
        \App\Models\EventOrderKeyword::where('order_id', $formInput['order_id'])->where('attendee_id', $formInput['attendee_id'])->delete();

        foreach($formInput['keywords'] as $keyword) {

            $modelData = array("order_id" => $formInput['order_id'], "attendee_id" => $formInput['attendee_id']);

            $keyword = json_decode($keyword, true);
            
            foreach($keyword['children'] as $children) {

                if($children['status'] == 1) {
                    $modelData['keyword_id'] = $children['id'];
                    \App\Models\EventOrderKeyword::create($modelData);
                }
                
            }

        }
    }

    /**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public function getNetworkInterestKeywords($formInput, $id){

        $attendeeRegFormId = \App\Eventbuizz\Repositories\AttendeeRepository::getAttendeeRegFormId($id, $formInput['event_id']);
        if($attendeeRegFormId === null){
            return [];
        }

		$keywords = array();
		$parents = \App\Models\MatchMaking::where('event_id', '=', $formInput['event_id'])->where('registration_form_id', $attendeeRegFormId)->where('parent_id', '=', '0')->with(['keywords' => function ($q) use($id) {
			return $q->where('attendee_id', '=', $id);
		}])
		->orderBy('sort_order', 'ASC')->get()->toArray();
		foreach ($parents as $parent) {
			$children = \App\Models\MatchMaking::where('parent_id', '=', $parent['id'])->with(['keywords' => function ($q) use($id) {
				return $q->where('attendee_id', '=', $id);
			}])->orderBy('sort_order', 'ASC')->get()->toArray();
			$parent['children'] = array();
			foreach ($children as $child) {
				$parent['children'][] = $child;
			}
			$keywords[] = $parent;

		}
		return $keywords;
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public function updateNetworkInterestKeywords($formInput, $id)
	{
        $event_id = $formInput['event_id'];
        \App\Models\AttendeeMatchKeyword::where('event_id', '=', $event_id)->where('attendee_id', '=', $id)->delete();
        if (isset($formInput['keywords']) && $formInput['keywords'] != '') {
            foreach ($formInput['keywords'] as $key) {
                $attendee_match_object = new \App\Models\AttendeeMatchKeyword();
                $attendee_match_object->organizer_id = 0;
                $attendee_match_object->event_id = $event_id;
                $attendee_match_object->attendee_id = $id;
                $attendee_match_object->keyword_id = $key;
                $attendee_match_object->status = 1;
                $attendee_match_object->save();
				info($attendee_match_object);
            }
        }
	}

    /**
     * @param mixed $formInput
     *
     * @return [type]
     */
    public function getAttendeeKeywords($formInput)
    {
        return  \App\Models\EventOrderKeyword::where('order_id', $formInput['order_id'])->where('attendee_id', $formInput['attendee_id'])->get();
    }

}
