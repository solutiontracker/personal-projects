<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;

class BadgeRepository extends AbstractRepository
{
	private $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 *EventBadge clone/default
	 *
	 * @param array
	 */
	public function install($request) {
		$setting = \App\Models\EventBadge::where('event_id', $request['from_event_id'])->get();
		if (count($setting)) {
			foreach ($setting as $record) {
				$record = $record->replicate();
				$record->event_id = $request['to_event_id'];
				$record->save();
			}
		} else {
			//Saveing Default Templates
			$formInput['event_id'] = $request['to_event_id'];
			$formInput['template_type'] = "1";
			$formInput['heading_color'] = 'rgb(0,0,0)';
			$formInput['company_color'] = 'rgb(109,110,113)';
			$formInput['tracks_color'] = 'rgb(109,110,113)';
			$formInput['delegate_Color'] = '';
			$formInput['table_Color'] = '';
			$formInput['logo'] = 'no-logo.png';
			$formInput['logoType'] = 'default';
			$formInput['footer_bg_color'] = 'rgb(19,103,167)';
			$formInput['footer_text_color'] = 'rgb(255,255,255)';
			\App\Models\EventBadge::create($formInput);

			$formInput['event_id'] = $request['to_event_id'];
			$formInput['template_type'] = "2";
			$formInput['heading_color'] = 'rgb(0,0,0)';
			$formInput['company_color'] = 'rgb(109,110,113)';
			$formInput['tracks_color'] = 'rgb(109,110,113)';
			$formInput['delegate_Color'] = '';
			$formInput['table_Color'] = '';
			$formInput['logo'] = 'no-logo.png';
			$formInput['logoType'] = 'default';
			$formInput['footer_bg_color'] = 'rgb(19,103,167)';
			$formInput['footer_text_color'] = 'rgb(255,255,255)';
			\App\Models\EventBadge::create($formInput);
		}

		return true;
	}

    /**
     * @param mixed $event_id
     * @param mixed $category
     *
     * @return [type]
     */
    static public function getBadgeDesign($event_id, $category)
    { 
        if ($category) {
            $design = \App\Models\EventBadgeDesign::where('event_id', $event_id)->where('type', $category)->first();
        } else {
            $design = \App\Models\EventBadgeDesign::where('event_id', $event_id)->first();

        }

        return $design;
    }

    /**
     * @param mixed $event_id
     * @param null $category
     *
     * @return [type]
     */
    static public function getBadgeDesignURL($event_id, $image_alias, $category = null)
    {
        $design = self::getBadgeDesign($event_id, $category);

        if ($design[$image_alias]) {
            if ($image_alias == "IsImage") {
                return cdn("/assets/badges/uploads/logo/" . $design[$image_alias]);
            } else {
                return cdn("/assets/badges/uploads/" . $design[$image_alias]);
            }
        }

        return;
    }
}