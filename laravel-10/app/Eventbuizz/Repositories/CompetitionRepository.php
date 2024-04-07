<?php

namespace App\Eventbuizz\Repositories;

use Illuminate\Http\Request;

class CompetitionRepository extends AbstractRepository
{
	private $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 *CompetitionSetting clone/default
	 *
	 * @param array
	 */
	public function install($request)
	{
		$setting = \App\Models\CompetitionSetting::where('event_id', $request['from_event_id'])->get();

		if (count($setting)) {
			foreach ($setting as $record) {
				$record = $record->replicate();
				$record->event_id = $request['to_event_id'];
				$record->save();
			}
		} else {
			$settings = array('template' => '<p>Deltag i konkurrencen om EventBuizz der samler hele jeres n&aelig;ste
            arrangement i &eacute;n enkelt app. Giv os navnet p&aring; de personer der laver m&oslash;der, events,
            seminarer eller konferencer hos jer.</p><p>Vinderen f&aring;r direkte besked. V&aelig;rdi 25.000 kr.</p>');

			$model = new \App\Models\CompetitionSetting();

			if (count($settings  ?? []) > 0) {
				foreach ($request['languages'] as $lang) {
					$setting = array();
					$setting['event_id'] = $request['to_event_id'];
					$setting['languages_id'] = $lang;
					foreach ($settings as $name => $value) {
						$setting[$name] = $value;
					}
					$model->create($setting);
				}
			}
		}
	}
}