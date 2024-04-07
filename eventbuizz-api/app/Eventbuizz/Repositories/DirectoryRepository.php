<?php

namespace App\Eventbuizz\Repositories;

use App\Models\DirectoryGroup;
use App\Models\EventGroup;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class DirectoryRepository extends AbstractRepository
{
	private $request;

	/**
	 * @param Request $request
	 */
	public function __construct(Request $request)
	{
		$this->request = $request;
	}

	/**
	 * @param mixed $request
	 * 
	 * @return [type]
	 */
	public function install($request)
	{
		//Event Directory
		$from_directory = \App\Models\Directory::where('event_id', $request['from_event_id'])->get();

		if (count($from_directory)) {
			//for cloning event
			foreach ($from_directory as $directory) {
				$directory_instance = \App\Models\Directory::find($directory->id)->replicate();
				$directory_instance->event_id = $request['to_event_id'];
				$directory_instance->save();

				$directory_info = \App\Models\DirectoryInfo::where('directory_id', $directory->id)->get();
				foreach ($directory_info as $info) {
					$directory_info_instance = \App\Models\DirectoryInfo::find($info->id)->replicate();
					$directory_info_instance->directory_id = $directory_instance->id;
					$directory_info_instance->save();
				}
			}
		} else {
			//for creating event
			$count = \App\Models\Directory::where('event_id', '=', $request['to_event_id'])->count();
			if ($count == 0) {
				for ($i = 0; $i < 5; $i++) {
					$formInput['created_at'] = \Carbon\Carbon::now();
					$formInput['updated_at'] = \Carbon\Carbon::now();
					$formInput['event_id'] = $request['to_event_id'];
					$formInput['parent_id'] = 0;
					if ($i == 4) {
						$formInput['other'] = 1;
					} else {
						$formInput['other'] = 0;
					}
					$formInput['agenda_id'] = 0;
					$formInput['speaker_id'] = 0;
					$formInput['sponsor_id'] = 0;
					$formInput['exhibitor_id'] = 0;
					$directory_created = \App\Models\Directory::create($formInput);
					foreach ($request['languages'] as $lang) {
						if ($lang == 1) {
							$formInput['directory_id'] = $directory_created->id;
							$formInput['languages_id'] = $lang;
							if ($i == 0) {
								$formInput['value'] = 'Program';
								$formInput['name'] = 'Program';
							} else if ($i == 1) {
								$formInput['value'] = 'Speakers';
								$formInput['name'] = 'Speakers';
							} else if ($i == 2) {
								$formInput['value'] = 'Sponsors';
								$formInput['name'] = 'Sponsors';
							} else if ($i == 3) {
								$formInput['value'] = 'Exhibitors';
								$formInput['name'] = 'Exhibitors';
							} else if ($i == 4) {
								$formInput['value'] = 'Other';
								$formInput['name'] = 'Other';
							}
							$formInput['created_at'] = \Carbon\Carbon::now();
							$formInput['updated_at'] = \Carbon\Carbon::now();
							$formInput['status'] = 1;
							\App\Models\DirectoryInfo::create($formInput);
						} elseif ($lang == 2) {
							$formInput['directory_id'] = $directory_created->id;
							$formInput['languages_id'] = $lang;
							if ($i == 0) {
								$formInput['value'] = 'Program';
								$formInput['name'] = 'Program';
							} else if ($i == 1) {
								$formInput['value'] = 'Talere';
								$formInput['name'] = 'Talere';
							} else if ($i == 2) {
								$formInput['value'] = 'Sponsors';
								$formInput['name'] = 'Sponsors';
							} else if ($i == 3) {
								$formInput['value'] = 'Udstillere';
								$formInput['name'] = 'Udstillere';
							} else if ($i == 4) {
								$formInput['value'] = 'Diverse';
								$formInput['name'] = 'Diverse';
							}
							$formInput['created_at'] = \Carbon\Carbon::now();
							$formInput['updated_at'] = \Carbon\Carbon::now();
							$formInput['status'] = 1;
							\App\Models\DirectoryInfo::create($formInput);
						} elseif ($lang == 3) {

							$formInput['directory_id'] = $directory_created->id;
							$formInput['languages_id'] = $lang;
							if ($i == 0) {
								$formInput['value'] = 'Program';
								$formInput['name'] = 'Program';
							} else if ($i == 1) {
								$formInput['value'] = 'Talere';
								$formInput['name'] = 'Talere';
							} else if ($i == 2) {
								$formInput['value'] = 'Sponsorer';
								$formInput['name'] = 'Sponsorer';
							} else if ($i == 3) {
								$formInput['value'] = 'Utstillere';
								$formInput['name'] = 'Utstillere';
							} else if ($i == 4) {
								$formInput['value'] = 'Annen';
								$formInput['name'] = 'Annen';
							}
							$formInput['created_at'] = \Carbon\Carbon::now();
							$formInput['updated_at'] = \Carbon\Carbon::now();
							$formInput['status'] = 1;
							\App\Models\DirectoryInfo::create($formInput);
						} elseif ($lang == 4) {
							$formInput['directory_id'] = $directory_created->id;
							$formInput['languages_id'] = $lang;
							if ($i == 0) {
								$formInput['value'] = 'Programm';
								$formInput['name'] = 'Programm';
							} else if ($i == 1) {
								$formInput['value'] = 'Sprecher';
								$formInput['name'] = 'Sprecher';
							} else if ($i == 2) {
								$formInput['value'] = 'Sponsoren';
								$formInput['name'] = 'Sponsoren';
							} else if ($i == 3) {
								$formInput['value'] = 'Aussteller';
								$formInput['name'] = 'Aussteller';
							} else if ($i == 4) {
								$formInput['value'] = 'Andere';
								$formInput['name'] = 'Andere';
							}
							$formInput['created_at'] = \Carbon\Carbon::now();
							$formInput['updated_at'] = \Carbon\Carbon::now();
							$formInput['status'] = 1;
							\App\Models\DirectoryInfo::create($formInput);
						} elseif ($lang == 5) {
							$formInput['directory_id'] = $directory_created->id;
							$formInput['languages_id'] = $lang;
							if ($i == 0) {
								$formInput['value'] = 'Programa';
								$formInput['name'] = 'Programa';
							} else if ($i == 1) {
								$formInput['value'] = 'Garsiakalbiai';
								$formInput['name'] = 'Garsiakalbiai';
							} else if ($i == 2) {
								$formInput['value'] = 'Rėmėjai';
								$formInput['name'] = 'Rėmėjai';
							} else if ($i == 3) {
								$formInput['value'] = 'Parodos dalyviai';
								$formInput['name'] = 'Parodos dalyviai';
							} else if ($i == 4) {
								$formInput['value'] = 'Kitas';
								$formInput['name'] = 'Kitas';
							}
							$formInput['created_at'] = \Carbon\Carbon::now();
							$formInput['updated_at'] = \Carbon\Carbon::now();
							$formInput['status'] = 1;
							\App\Models\DirectoryInfo::create($formInput);
						} elseif ($lang == 6) {
							$formInput['directory_id'] = $directory_created->id;
							$formInput['languages_id'] = $lang;
							if ($i == 0) {
								$formInput['value'] = 'Ohjelmoida';
								$formInput['name'] = 'Ohjelmoida';
							} else if ($i == 1) {
								$formInput['value'] = 'Kaiuttimet';
								$formInput['name'] = 'Kaiuttimet';
							} else if ($i == 2) {
								$formInput['value'] = 'Sponsorit';
								$formInput['name'] = 'Sponsorit';
							} else if ($i == 3) {
								$formInput['value'] = 'Näytteilleasettajat';
								$formInput['name'] = 'Näytteilleasettajat';
							} else if ($i == 4) {
								$formInput['value'] = 'Muut';
								$formInput['name'] = 'Muut';
							}
							$formInput['created_at'] = \Carbon\Carbon::now();
							$formInput['updated_at'] = \Carbon\Carbon::now();
							$formInput['status'] = 1;
							\App\Models\DirectoryInfo::create($formInput);
						} elseif ($lang == 7) {
							$formInput['directory_id'] = $directory_created->id;
							$formInput['languages_id'] = $lang;
							if ($i == 0) {
								$formInput['value'] = 'Program';
								$formInput['name'] = 'Program';
							} else if ($i == 1) {
								$formInput['value'] = 'Talare';
								$formInput['name'] = 'Talare';
							} else if ($i == 2) {
								$formInput['value'] = 'Sponsorer';
								$formInput['name'] = 'Sponsorer';
							} else if ($i == 3) {
								$formInput['value'] = 'Utställare';
								$formInput['name'] = 'Utställare';
							} else if ($i == 4) {
								$formInput['value'] = 'Andra';
								$formInput['name'] = 'Andra';
							}
							$formInput['created_at'] = \Carbon\Carbon::now();
							$formInput['updated_at'] = \Carbon\Carbon::now();
							$formInput['status'] = 1;
							\App\Models\DirectoryInfo::create($formInput);
						} elseif ($lang == 8) {
							$formInput['directory_id'] = $directory_created->id;
							$formInput['languages_id'] = $lang;
							if ($i == 0) {
								$formInput['value'] = 'Programma';
								$formInput['name'] = 'Programma';
							} else if ($i == 1) {
								$formInput['value'] = 'Sprekers';
								$formInput['name'] = 'Sprekers';
							} else if ($i == 2) {
								$formInput['value'] = 'Sponsoren';
								$formInput['name'] = 'Sponsoren';
							} else if ($i == 3) {
								$formInput['value'] = 'Exposanten';
								$formInput['name'] = 'Exposanten';
							} else if ($i == 4) {
								$formInput['value'] = 'Anders';
								$formInput['name'] = 'Anders';
							}
							$formInput['created_at'] = \Carbon\Carbon::now();
							$formInput['updated_at'] = \Carbon\Carbon::now();
							$formInput['status'] = 1;
							\App\Models\DirectoryInfo::create($formInput);
						} elseif ($lang == 9) {
							$formInput['directory_id'] = $directory_created->id;
							$formInput['languages_id'] = $lang;
							if ($i == 0) {
								$formInput['value'] = 'Programma';
								$formInput['name'] = 'Programma';
							} else if ($i == 1) {
								$formInput['value'] = 'Sprekers';
								$formInput['name'] = 'Sprekers';
							} else if ($i == 2) {
								$formInput['value'] = 'Sponsors';
								$formInput['name'] = 'Sponsors';
							} else if ($i == 3) {
								$formInput['value'] = 'Exposanten';
								$formInput['name'] = 'Exposanten';
							} else if ($i == 4) {
								$formInput['value'] = 'Anders';
								$formInput['name'] = 'Anders';
							}
							$formInput['created_at'] = \Carbon\Carbon::now();
							$formInput['updated_at'] = \Carbon\Carbon::now();
							$formInput['status'] = 1;
							\App\Models\DirectoryInfo::create($formInput);
						}
					}
				}
			}
		}
		//Event Settings
		$from_directory_setting = \App\Models\DirectorySetting::where('event_id', $request['from_event_id'])->get();
		if (count($from_directory_setting)) {
			//for cloning event
			foreach ($from_directory_setting as $setting) {
				$setting_instance = \App\Models\DirectorySetting::where('event_id', $request['to_event_id'])->where('name', $setting->name)->where('languages_id', $setting->languages_id)->first();
				if ($setting_instance) {
					$setting_instance->value = $setting->value;
					$setting_instance->save();
				} else {
					$setting_instance = \App\Models\DirectorySetting::find($setting->id)->replicate();
					$setting_instance->event_id = $request['to_event_id'];
					$setting_instance->save();
				}
			}
		} else {
			//for creating event
			$count = \App\Models\DirectorySetting::where('event_id', '=', $request['to_event_id'])->count();
			if ($count == 0) {
				foreach ($request['languages'] as $lang) {
					if ($lang == '1') {
						$setting['name'] = 'No document available';
						$setting['value'] = 'No document available';
						$setting['alies'] = 'document';
						$setting['event_id'] = $request['to_event_id'];
						$setting['languages_id'] = $lang;
						\App\Models\DirectorySetting::create($setting);

						// Email Label
						$setting['name'] = 'Email this document';
						$setting['value'] = 'Email this document';
						$setting['alies'] = 'email';
						$setting['event_id'] = $request['to_event_id'];
						$setting['languages_id'] = $lang;
						\App\Models\DirectorySetting::create($setting);
					} elseif ($lang == '2') {
						$setting['name'] = 'Ingen tilgængelige dokumenter';
						$setting['value'] = 'Ingen tilgængelige dokumenter';
						$setting['alies'] = 'document';
						$setting['event_id'] = $request['to_event_id'];
						$setting['languages_id'] = $lang;
						\App\Models\DirectorySetting::create($setting);

						// Email Label
						$setting['name'] = 'Send dokument med email';
						$setting['value'] = 'Send dokument med email';
						$setting['alies'] = 'email';
						$setting['event_id'] = $request['to_event_id'];
						$setting['languages_id'] = $lang;
						\App\Models\DirectorySetting::create($setting);
					} elseif ($lang == '3') {

						$setting['name'] = 'Ingen dokumenter tilgjengelig';
						$setting['value'] = 'Ingen dokumenter tilgjengelig';
						$setting['alies'] = 'document';
						$setting['event_id'] = $request['to_event_id'];
						$setting['languages_id'] = $lang;
						\App\Models\DirectorySetting::create($setting);

						// Email Label
						$setting['name'] = 'Send dette dokumentet';
						$setting['value'] = 'Send dette dokumentet';
						$setting['alies'] = 'email';
						$setting['event_id'] = $request['to_event_id'];
						$setting['languages_id'] = $lang;
						\App\Models\DirectorySetting::create($setting);
					} elseif ($lang == '4') {

						$setting['name'] = 'Kein Dokument verfügbar';
						$setting['value'] = 'Kein Dokument verfügbar';
						$setting['alies'] = 'document';
						$setting['event_id'] = $request['to_event_id'];
						$setting['languages_id'] = $lang;
						\App\Models\DirectorySetting::create($setting);

						// Email Label
						$setting['name'] = 'E-Mail an dieses Dokument';
						$setting['value'] = 'E-Mail an dieses Dokument';
						$setting['alies'] = 'email';
						$setting['event_id'] = $request['to_event_id'];
						$setting['languages_id'] = $lang;
						\App\Models\DirectorySetting::create($setting);
					} elseif ($lang == '5') {

						$setting['name'] = 'Nėra dokumentą galima';
						$setting['value'] = 'Nėra dokumentą galima';
						$setting['alies'] = 'document';
						$setting['event_id'] = $request['to_event_id'];
						$setting['languages_id'] = $lang;
						\App\Models\DirectorySetting::create($setting);

						// Email Label
						$setting['name'] = 'Siųsti šį dokumentą';
						$setting['value'] = 'Siųsti šį dokumentą';
						$setting['alies'] = 'email';
						$setting['event_id'] = $request['to_event_id'];
						$setting['languages_id'] = $lang;
						\App\Models\DirectorySetting::create($setting);
					} elseif ($lang == '6') {
						$setting['name'] = 'Ei asiakirjaan';
						$setting['value'] = 'Ei asiakirjaan';
						$setting['alies'] = 'document';
						$setting['event_id'] = $request['to_event_id'];
						$setting['languages_id'] = $lang;
						\App\Models\DirectorySetting::create($setting);

						// Email Label
						$setting['name'] = 'Lähetä tämä asiakirja';
						$setting['value'] = 'Lähetä tämä asiakirja';
						$setting['alies'] = 'email';
						$setting['event_id'] = $request['to_event_id'];
						$setting['languages_id'] = $lang;
						\App\Models\DirectorySetting::create($setting);
					} elseif ($lang == '7') {
						$setting['name'] = 'Inga dokument tillgängliga';
						$setting['value'] = 'Inga dokument tillgängliga';
						$setting['alies'] = 'document';
						$setting['event_id'] = $request['to_event_id'];
						$setting['languages_id'] = $lang;
						\App\Models\DirectorySetting::create($setting);

						// Email Label
						$setting['name'] = 'E-posta det här dokumentet';
						$setting['value'] = 'E-posta det här dokumentet';
						$setting['alies'] = 'email';
						$setting['event_id'] = $request['to_event_id'];
						$setting['languages_id'] = $lang;
						\App\Models\DirectorySetting::create($setting);
					} elseif ($lang == '8') {
						$setting['name'] = 'Geen document beschikbaar';
						$setting['value'] = 'Geen document beschikbaar';
						$setting['alies'] = 'document';
						$setting['event_id'] = $request['to_event_id'];
						$setting['languages_id'] = $lang;
						\App\Models\DirectorySetting::create($setting);

						// Email Label
						$setting['name'] = 'Verzend dit document per e-mail';
						$setting['value'] = 'Verzend dit document per e-mail';
						$setting['alies'] = 'email';
						$setting['event_id'] = $request['to_event_id'];
						$setting['languages_id'] = $lang;
						\App\Models\DirectorySetting::create($setting);
					} elseif ($lang == '9') {
						$setting['name'] = 'Geen document beschikbaar';
						$setting['value'] = 'Geen document beschikbaar';
						$setting['alies'] = 'document';
						$setting['event_id'] = $request['to_event_id'];
						$setting['languages_id'] = $lang;
						\App\Models\DirectorySetting::create($setting);

						// Email Label
						$setting['name'] = 'Verzend dit document per e-mail';
						$setting['value'] = 'Verzend dit document per e-mail';
						$setting['alies'] = 'email';
						$setting['event_id'] = $request['to_event_id'];
						$setting['languages_id'] = $lang;
						\App\Models\DirectorySetting::create($setting);
					}
				}
			}
		}

		return true;
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	static public function subModules($formInput)
	{
		$menus = array();
		$directories = \App\Models\Directory::where('event_id', $formInput['event_id'])
			->where('parent_id', '0')
			->with(['info' => function ($query) use ($formInput) {
				return $query->where('languages_id', $formInput['language_id']);
			}])->get();

		foreach ($directories as $dir) {
			$formInput['alias'] = "agendas";
			$agenda = EventSettingRepository::getEventModule($formInput);

			$formInput['alias'] = "speakers";
			$speaker = EventSettingRepository::getEventModule($formInput);

			$formInput['alias'] = "sponsors";
			$sponsor = EventSettingRepository::getEventModule($formInput);

			$formInput['alias'] = "exhibitors";
			$exhibitor = EventSettingRepository::getEventModule($formInput);

			if (in_array($dir->info[0]->value, ['Program', 'Programa', 'Ohjelmoida', 'Programm'])) {
				$menus[] = array(
					'id' => $dir->id,
					'name' => $agenda->info[0]['value'],
					'alias' => "agendas",
					'status' => $agenda->status,
				);
			} elseif (in_array($dir->info[0]->value, ['Speakers', 'Talere', 'Talare', 'Lautsprecher', 'Garsiakalbiai', 'Sprecher', 'Kaiuttimet', 'Høyttalere'])) {
				$menus[] = array(
					'id' => $dir->id,
					'name' => $speaker->info[0]['value'],
					'alias' => "speakers",
					'status' => $speaker->status
				);
			} elseif (in_array($dir->info[0]->value, ['Exhibitors', 'Udstillere', 'Utställare', 'Parodos dalyviai', 'Näytteilleasettajat', 'Utstillere', 'Aussteller'])) {
				$menus[] = array(
					'id' => $dir->id,
					'name' => $exhibitor->info[0]['value'],
					'alias' => "exhibitors",
					'status' => $exhibitor->status
				);
			} elseif (in_array($dir->info[0]->value, ['Sponsors', 'Rėmėjai', 'Sponsoren', 'Sponsorer', 'Sponsorit'])) {
				$menus[] = array(
					'id' => $dir->id,
					'name' => $sponsor->info[0]['value'],
					'alias' => "sponsors",
					'status' => $sponsor->status
				);
			} else {
				$menus[] = array(
					'id' => $dir->id,
					'name' => $dir->info[0]->value,
					'alias' => "other",
					'status' => 1
				);
			}
		}

		return $menus;
	}

	/**
	 * @param mixed $formInput
	 * @param mixed $id
	 * 
	 * @return [type]
	 */
	public function listing($formInput, $id, $delete_id = null)
	{
		$query = \App\Models\Directory::where('event_id', $formInput['event_id']);
		if ($delete_id) {
			$query->where('id', $delete_id);
		} else {
			$query->where(function ($query) use ($id) {
				$query->where('parent_id', 0)
					->orwhere('id', '=', $id);
			});
		}
		$directories = $query->with(['info' => function ($query) use ($formInput) {
			return $query->where('languages_id', $formInput['language_id']);
		}])
			->with(['files.info' => function ($query) use ($formInput) {
				return $query->where('languages_id', $formInput['language_id']);
			}])
			->with('childrenRecursiveWithFiles.files')
			->with('groups')
			->orderby('sort_order', 'ASC')
			->get();

		$response = array();
		foreach ($directories as $dir) {

			//Define alias for default directories
			if (in_array($dir->info[0]->value, ['Program', 'Programa', 'Ohjelmoida', 'Programm'])) {
				$alias = "agendas";
			} elseif (in_array($dir->info[0]->value, ['Speakers', 'Talere', 'Talare', 'Lautsprecher', 'Garsiakalbiai', 'Sprecher', 'Kaiuttimet', 'Høyttalere'])) {
				$alias = "speakers";
			} elseif (in_array($dir->info[0]->value, ['Exhibitors', 'Udstillere', 'Utställare', 'Parodos dalyviai', 'Näytteilleasettajat', 'Utstillere', 'Aussteller'])) {
				$alias = "exhibitors";
			} elseif (in_array($dir->info[0]->value, ['Sponsors', 'Rėmėjai', 'Sponsoren', 'Sponsorer', 'Sponsorit'])) {
				$alias = "sponsors";
			} else {
				$alias = "other";
			}

			$array = array(
				'id' => $dir->id,
				'parent_id' => 0,
				'sort_order' => $dir->sort_order,
				'name' => $dir->info[0]->value,
				'start_date' => "",
				'start_time' => "",
				'updated_date' => \Carbon\Carbon::parse($dir->updated_at)->toDateString(),
				'updated_time' => \Carbon\Carbon::parse($dir->updated_at)->format('H:i'),
				'type' => "folder",
				'groups' => $dir->groups,
				'alias' => $alias
			);

			$response[] = $array;
			if (count($dir->files) > 0) {
				foreach ($dir->files as $file) {
					if ($file->s3 == 1) {
						$url = getS3Image('assets/directory/' . $file->path);
					} else {
						$url = route('wizard-directory-download-document-file', [$formInput['module'], $file->id]);
					}

					$array = array(
						'id' => $file->id,
						'parent_id' => $file->directory_id,
						'sort_order' => $file->sort_order,
						's3' => $file->s3,
						'name' => $file->info[0]->value,
						'start_date' => $file->start_date,
						'start_time' => \Carbon\Carbon::parse($file->start_time)->format('H:i'),
						'path' => $file->path,
						'updated_date' => ($file->start_date != "0000-00-00" ? \Carbon\Carbon::parse($file->start_date)->toDateString() : \Carbon\Carbon::now()->toDateString()),
						'updated_time' => ($file->start_time != "00:00:00" ? \Carbon\Carbon::parse($file->start_time)->format('H:i') : \Carbon\Carbon::now()->format('H:i')),
						'type' => "file",
						'url' => $url
					);
					$response[] = $array;
				}
			}
			if (count($dir->childrenRecursiveWithFiles) > 0) {
				$response = $this->readHierarchy($formInput, $dir->childrenRecursiveWithFiles, $response);
			}
		}

		$response = array_values(Arr::sort($response, function ($value) {
			return $value['sort_order'];
		}));

		return $response;
	}

	/**
	 * @param mixed $data
	 * @param mixed $response
	 * 
	 * @return [type]
	 */
	public function readHierarchy($formInput, $data, $response)
	{
		foreach ($data as $dir) {
			$info = $dir->info()->where('languages_id', $formInput['language_id'])->first();
			$array = array(
				'id' => $dir->id,
				'parent_id' => $dir->parent_id,
				'sort_order' => $dir->sort_order,
				'name' => $info->value,
				'start_date' => "",
				'start_time' => "",
				'updated_date' => \Carbon\Carbon::parse($dir->updated_at)->toDateString(),
				'updated_time' => \Carbon\Carbon::parse($dir->updated_at)->format('H:i'),
				'type' => "folder",
				'groups' => $dir->groups,
				'alias' => "child"
			);
			$response[] = $array;
			if (count($dir->files) > 0) {
				foreach ($dir->files as $file) {
					$info = $file->info()->where('languages_id', $formInput['language_id'])->first();
					if ($file->s3 == 1) {
						$url = getS3Image('assets/directory/' . $file->path);
					} else {
						$url = route('wizard-directory-download-document-file', [$formInput['module'], $file->id]);
					}
					$array = array(
						'id' => $file->id,
						'parent_id' => $file->directory_id,
						'sort_order' => $file->sort_order,
						's3' => $file->s3,
						'name' => $info->value,
						'start_date' => $file->start_date,
						'start_time' => \Carbon\Carbon::parse($file->start_time)->format('H:i'),
						'path' => $file->path,
						'updated_date' => ($file->start_date != "0000-00-00" ? \Carbon\Carbon::parse($file->start_date)->toDateString() : \Carbon\Carbon::now()->toDateString()),
						'updated_time' => ($file->start_time != "00:00:00" ? \Carbon\Carbon::parse($file->start_time)->format('H:i') : \Carbon\Carbon::now()->format('H:i')),
						'type' => "file",
						'url' => $url
					);
					$response[] = $array;
				}
			}
			if (count($dir->childrenRecursiveWithFiles) > 0) {
				$response = $this->readHierarchy($formInput, $dir->childrenRecursiveWithFiles, $response);
			}
		}

		return $response;
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public function addDocument($formInput)
	{
		$max = \App\Models\Directory::where('event_id', '=', $formInput['event_id'])->max('sort_order');
		$formInput['sort_order'] = ($max + 1);
		$directory = \App\Models\Directory::create($formInput);
		$languages = get_event_languages($formInput['event_id']);
		foreach ($languages as $language_id) {
			$formInput['directory_id'] = $directory->id;
			$formInput['languages_id'] = $language_id;
			$formInput['value'] = $formInput['name'];
			$formInput['name'] = 'name';
			$formInput['status'] = 1;
			\App\Models\DirectoryInfo::create($formInput);
		}
		if ($directory) {
			foreach ($formInput['groups'] as $key => $value) {
				DirectoryGroup::create([
					'directory_id' => $directory->id,
					'group_id' => $value,
				]);
			}
		}
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public function updateDocument($formInput)
	{
		$info = \App\Models\DirectoryInfo::where('name', 'name')->where('directory_id', $formInput["id"])->where('languages_id', $formInput['language_id'])->first();
		if ($info) {
			$info->value = $formInput['name'];
			$info->save();
			$directory = \App\Models\Directory::find($formInput["id"]);
			$directory->save();
		}
		DirectoryGroup::where('directory_id', $formInput['id'])->delete();
		foreach ($formInput['groups'] as $key => $value) {
			DirectoryGroup::create([
				'directory_id' => $formInput['id'],
				'group_id' => $value,
			]);
		}
		return $info;
	}

	/**
	 * @param mixed $id
	 * 
	 * @return [type]
	 */
	public function destroyDocument($id)
	{
		$directory = \App\Models\Directory::find($id);
		if ($directory) {
			$directory->delete();
		}
	}

	/**
	 * @param mixed $id
	 * 
	 * @return [type]
	 */
	public function destroyDocumentFile($id)
	{
		$file = \App\Models\DirectoryFile::find($id);
		if ($file) {
			if ($file->s3 == 1) {
				deleteObject('assets/directory/' . $file->path);
			} else {
				deleteFile(config('cdn.cdn_upload_path') . 'assets/directory/' . $file->path);
			}
			$file->delete();
		}
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public function uploadDocument($formInput)
	{
		foreach ($formInput['files'] as $file) {
			$file_name = 'document_' . time() . '.' . $file->getClientOriginalExtension();
			if (in_array(config('app.env'), ["production"])) {
				$file->storeAs(
					'assets/directory',
					$file_name,
					's3'
				);
				$formInput['s3'] = 1;
				$formInput['file_size'] = (int) (\Storage::disk('s3')->exists('assets/directory/' . $file_name) ? \Storage::disk('s3')->size('assets/directory/' . $file_name) : 0);
			} else {
				$file->move(config('cdn.cdn_upload_path') . '/assets/directory/', $file_name);
				$formInput['s3'] = 0;
				$formInput['file_size'] = (int) \File::size(config('cdn.cdn_upload_path') . '/assets/directory/' . $file_name);
			}

			$formInput['path'] = $file_name;
			$formInput['file_name'] = $file->getClientOriginalName();
			$formInput['organizer_id'] = organizer_id();
			$formInput['parent_id'] = 0;

			//add file 
			$this->addFile($formInput);
		}
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public function addFile($formInput)
	{
		$max = \App\Models\DirectoryFile::where('directory_id', '=', $formInput['directory_id'])->max('sort_order');
		$formInput['sort_order'] = ($max + 1);
		$file = \App\Models\DirectoryFile::create($formInput);
		$languages = get_event_languages($formInput['event_id']);
		foreach ($languages as $language_id) {
			$formInput['file_id'] = $file->id;
			$formInput['languages_id'] = $language_id;
			$formInput['value'] = $formInput['file_name'];
			$formInput['name'] = 'name';
			$formInput['status'] = 1;
			\App\Models\FileInfo::create($formInput);
		}
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public function renameDocumentFile($formInput)
	{
		$info = \App\Models\FileInfo::where('file_id', $formInput['id'])->where('languages_id', $formInput['language_id'])->first();
		if ($info) {
			$info->value = $formInput['name'];
			$info->save();
			$file = \App\Models\DirectoryFile::find($formInput['id']);
			$file->save();
		}
		return $info;
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public function scheduleDocument($formInput)
	{
		$file = \App\Models\DirectoryFile::find($formInput['id']);
		if ($file) {
			$file->start_date = \Carbon\Carbon::parse($formInput['start_date'])->toDateString();
			$file->start_time = \Carbon\Carbon::parse($formInput['start_time'])->toTimeString();
			$file->save();
		}
		return $file;
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public function moveFile($formInput)
	{
		$organizer_id = organizer_id();
		$file = \App\Models\DirectoryFile::where('id', $formInput["from_id"])->with('info')->first();
		$directory = \App\Models\Directory::where('id', $formInput["to_id"])->with('info')->first();
		if (!($directory->parent_id == '0' && $directory->other != '1')) {
			//check if file exists in same directory
			$count = \App\Models\DirectoryFile::where('directory_id', $directory->id)->where('path', $file->path)->count();
			if ($count == 0) {
				$parts = explode(".", $file->path);
				$file_name = 'document_' . time() . '.' . $parts[1];
				if ($file->s3 == 0) {
					moveFile(
						config('cdn.cdn_upload_path') . '/assets/directory/' . $file->path,
						config('cdn.cdn_upload_path') . '/assets/directory/' . $file_name
					);
				} else {
					moveObject('assets/directory/' . $file->path, 'assets/directory/' . $file_name);
				}
				$formData['path'] = $file_name;
				$formData['organizer_id'] = $organizer_id;
				$formData['directory_id'] = $directory->id;
				$formData['parent_id'] = $file->parent_id;
				$formData['file_size'] = $file->file_size;
				$formData['start_date'] = $file->start_date;
				$formData['start_time'] = $file->start_time;
				$formData['s3'] = $file->s3;
				$file_created = \App\Models\DirectoryFile::create($formData);
				$languages = get_event_languages($formInput['event_id']);
				foreach ($languages as $language_id) {
					$infoData['file_id'] = $file_created->id;
					$infoData['languages_id'] = $language_id;
					$infoData['value'] = $file->info[0]['value'];
					$infoData['name'] = 'name';
					$infoData['status'] = 1;
					\App\Models\FileInfo::create($infoData);
				}
			}
			\App\Models\DirectoryFile::where('id', $formInput["from_id"])->delete();
		}
	}

	/**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public function copyFile($formInput)
	{
		$organizer_id = organizer_id();
		$file = \App\Models\DirectoryFile::where('id', $formInput["from_id"])->with('info')->first();
		$directory = \App\Models\Directory::where('id', $formInput["to_id"])->with('info')->first();
		if (!($directory->parent_id == '0' && $directory->other != '1')) {
			//check if file exists in same directory
			$count = \App\Models\DirectoryFile::where('directory_id', $directory->id)->where('path', $file->path)->count();
			if ($count == 0) {
				$parts = explode(".", $file->path);
				$file_name = 'document_' . time() . '.' . $parts[1];
				if ($file->s3 == 0) {
					copyFile(
						config('cdn.cdn_upload_path') . '/assets/directory/' . $file->path,
						config('cdn.cdn_upload_path') . '/assets/directory/' . $file_name
					);
				} else {
					copyObject('assets/directory/' . $file->path, 'assets/directory/' . $file_name);
				}
				$formData['path'] = $file_name;
				$formData['organizer_id'] = $organizer_id;
				$formData['directory_id'] = $directory->id;
				$formData['parent_id'] = $file->parent_id;
				$formData['file_size'] = $file->file_size;
				$formData['start_date'] = $file->start_date;
				$formData['start_time'] = $file->start_time;
				$formData['s3'] = $file->s3;
				$file_created = \App\Models\DirectoryFile::create($formData);
				$languages = get_event_languages($formInput['event_id']);
				foreach ($languages as $language_id) {
					$infoData['file_id'] = $file_created->id;
					$infoData['languages_id'] = $language_id;
					$infoData['value'] = $file->info[0]['value'];
					$infoData['name'] = 'name';
					$infoData['status'] = 1;
					\App\Models\FileInfo::create($infoData);
				}
			}
		}
	}
	/**
	 * @param mixed $formInput
	 * @param bool $label
	 * 
	 * @return [type]
	 */
	public function getPrograms($formInput, $label = false)
	{
		$programs = array();
		$result = \App\Models\EventAgenda::leftJoin('conf_agenda_info AS a_end_time', function ($join) use ($formInput) {
			$join->on('conf_event_agendas.id', '=', 'a_end_time.agenda_id')
				->where('a_end_time.name', '=', 'end_time')
				->where('a_end_time.languages_id', $formInput['language_id']);
		})
			->where('conf_event_agendas.event_id', $formInput['event_id'])
			->with(['info' => function ($query) use ($formInput) {
				return $query->where('languages_id', $formInput['language_id'])->where('name', '=', 'topic');
			}])
			->whereNull('conf_event_agendas.deleted_at')
			->orderBy('conf_event_agendas.start_date', 'ASC')
			->orderBy('conf_event_agendas.start_time', 'ASC')
			->orderBy('end_time', 'ASC')
			->orderBy('conf_event_agendas.created_at', 'ASC')
			->groupBy('conf_event_agendas.id')
			->select(array('conf_event_agendas.*',  'a_end_time.value as end_time'))
			->get();

		foreach ($result as $key => $program) {
			$id = $program->id;
			$name = $program->info[0]->value;
			if ($label) {
				$programs[$key]['value'] = $id;
				$programs[$key]['label'] = $name;
			} else {
				$programs[$key]['id'] = $id;
				$programs[$key]['name'] = $name;
			}
		}
		return $programs;
	}

	/**
	 * @param mixed $formInput
	 * @param bool $label
	 * 
	 * @return [type]
	 */
	public function getSpeakers($formInput, $label = false)
	{
		$speakers = array();
		$result = \App\Models\EventAgendaSpeaker::where('event_id', $formInput['event_id'])->with('attendee')->groupBy('attendee_id')->get();
		foreach ($result as $key => $speaker) {
			$id = $speaker->id;
			$name = $speaker->attendee->first_name . ' ' . $speaker->attendee->last_name;
			if ($label) {
				$speakers[$key]['value'] = $id;
				$speakers[$key]['label'] = $name;
			} else {
				$speakers[$key]['id'] = $id;
				$speakers[$key]['name'] = $name;
			}
		}
		return $speakers;
	}

	/**
	 * @param mixed $formInput
	 * @param bool $label
	 * 
	 * @return [type]
	 */
	public function getSponsors($formInput, $label = false)
	{
		$sponsors = array();
		$result = \App\Models\EventSponsor::where('event_id', $formInput['event_id'])->orderby('name')->get();
		foreach ($result as $key => $sponsor) {
			$id = $sponsor->id;
			$name = $sponsor->attendee->name;
			if ($label) {
				$sponsors[$key]['value'] = $id;
				$sponsors[$key]['label'] = $name;
			} else {
				$sponsors[$key]['id'] = $id;
				$sponsors[$key]['name'] = $name;
			}
		}
		return $sponsors;
	}

	/**
	 * @param mixed $formInput
	 * @param bool $label
	 * 
	 * @return [type]
	 */
	public function getExhibitors($formInput, $label = false)
	{
		$exhibitors = array();
		$result = \App\Models\EventExhibitor::where('event_id', $formInput['event_id'])->orderby('name')->get();
		foreach ($result as $key => $exhibitor) {
			$id = $exhibitor->id;
			$name = $exhibitor->attendee->name;
			if ($label) {
				$exhibitors[$key]['value'] = $id;
				$exhibitors[$key]['label'] = $name;
			} else {
				$exhibitors[$key]['id'] = $id;
				$exhibitors[$key]['name'] = $name;
			}
		}
		return $exhibitors;
	}

	/**
	 * @param mixed $formInput
	 * @param mixed $id
	 * 
	 * @return [type]
	 */
	public function getFile($formInput, $id)
	{
		return \App\Models\DirectoryFile::find($id);
	}


	public function getParentDirectories($formInput)
	{
		$event_id = $formInput['event_id'];
		$lang_id = $formInput['language_id'];

		$directories = \App\Models\Directory::where('event_id', '=', $event_id)->where('parent_id',  0)->with(['info' => function ($query) use ($lang_id) {
			return $query->where('languages_id', $lang_id);
		}])->with(['files', 'files.info' => function ($query) use ($lang_id) {
			return $query->where('languages_id', $lang_id);
		}])->orderby('sort_order', 'ASC')->get()->toArray();

		foreach ($directories as $key => $value) {
			foreach ($value['info'] as $item) {
				$value[$item['name']] = $item['value'];
			}
			unset($value['info']);
			foreach ($value['files'] as $ikey => $file) {
				foreach ($file['info'] as $item) {
					$file[$item['name']] = $item['value'];
				}
				unset($file['info']);

				if ($file['s3'] == 1) {
					$file['s3_url'] = getS3Image('assets/directory/' . $file['path']);
				}

				$value['files'][$ikey] = $file;
			}
			$directories[$key] = $value;

			$children = $this->getBreadcrumb($value['id'], $event_id, $lang_id);
			$new_array = array_values(Arr::sort(array_merge($children, $value['files']), function ($value) {
				return $value['sort_order'];
			}));
			$directories[$key]['children'] = $children;
			$directories[$key]['children_files'] = $new_array;
		}

		return $directories;
	}
	public function getOtherParentDirectoryFront($formInput)
	{
		$event_id = $formInput['event_id'];
		$lang_id = $formInput['language_id'];
		$directories = \App\Models\Directory::where('event_id', '=', $event_id)->where('parent_id',  0)->where('other',  1)->with(['info' => function ($query) use ($lang_id) {
			return $query->where('languages_id', $lang_id);
		}])->with(['files', 'files.info' => function ($query) use ($lang_id) {
			return $query->where('languages_id', $lang_id);
		}])->orderby('sort_order', 'ASC')->get();

		foreach ($directories as $key => $value) {
			foreach ($value['info'] as $item) {
				$value[$item['name']] = $item['value'];
			}
			unset($value['info']);
			foreach ($value['files'] as $file) {
				foreach ($file['info'] as $item) {
					$file[$item['name']] = $item['value'];
				}
				unset($file['info']);
			}
			$directories[$key] = $value;
			$directories[$key]['children_files'] = $this->getBreadcrumb($value['id'], $event_id, $lang_id);
		}
		return $directories;
	}

	/**
	 * getBreadcrumb
	 *
	 * @param  mixed $id
	 * @param  mixed $event_id
	 * @param  mixed $lang_id
	 * @return void
	 */
	public function getBreadcrumb($id, $event_id, $lang_id)
	{
		$directory_type = \App\Models\Directory::where('parent_id', $id)->first();

		$query = \App\Models\Directory::where('conf_directory.event_id', '=', $event_id)->where('conf_directory.parent_id', $id)
		->with(['info' => function ($q) use ($lang_id) {
			return $q->where('languages_id', $lang_id);
		}])
		->with(['groups','files' => function($query){
			$query->where(DB::raw("CONCAT(start_date, ' ', start_time)"),'<=', \Carbon\Carbon::now()->format('Y-m-d H:i:s'));
		}, 'files.info' => function ($q) use ($lang_id) {
			return $q->where('languages_id', $lang_id);
		}]);

		if ($directory_type->agenda_id <> 0) {
			$query = $query->join('conf_event_agendas','conf_directory.agenda_id', '=', 'conf_event_agendas.id')
			->leftJoin('conf_agenda_info',function($join){
				$join->on('conf_event_agendas.id', '=', 'conf_agenda_info.agenda_id')->where('conf_agenda_info.name', '=', 'topic');
			})
			->orderby('conf_event_agendas.start_date', 'ASC')
			->orderby('conf_event_agendas.start_time', 'ASC')
			->select('conf_directory.*','conf_event_agendas.id as agenda_id', 'conf_agenda_info.value as topic');
		} elseif ($directory_type->speaker_id <> 0) {

			$sort_order = request()['event']['speaker_settings']['order_by'];

			$query->join('conf_event_agenda_speakers', 'conf_directory.speaker_id', '=', 'conf_event_agenda_speakers.attendee_id')
				->join('conf_attendees', 'conf_attendees.id', '=', 'conf_directory.speaker_id')
				->groupBy('conf_event_agenda_speakers.attendee_id');

			if ($sort_order == 'custom') {
				$query->orderby('conf_event_agenda_speakers.sort_order', 'asc');
			} else {
				$query->orderby('conf_attendees.'. $sort_order,'asc');
			}

			$query->select('conf_directory.*',DB::raw('CONCAT(conf_attendees.first_name, " ", conf_attendees.last_name) as topic'));

		} elseif ($directory_type->exhibitor_id <> 0) {

			$sort_order = request()['event']['exhibitor_settings']['sortType'];

			$query->join('conf_event_exhibitors as exhibitor', 'conf_directory.exhibitor_id', '=', 'exhibitor.id')
				->leftJoin('conf_event_exhibitor_categories AS exhibitor_cat',function($j) {
					$j->on('exhibitor.id', '=', 'exhibitor_cat.exhibitor_id')->whereNull('exhibitor_cat.deleted_at');
				})
				->leftJoin('conf_event_categories AS ec', 'ec.id', '=', 'exhibitor_cat.category_id');
			if($sort_order == 1){
				$query->orderByRaw('ISNULL(ec.sort_order),ec.sort_order ASC')->orderby('exhibitor.name', 'ASC');
			}else{
				$query->orderby('exhibitor.name', 'ASC');
			}

			$query->groupBy('conf_directory.exhibitor_id')->select('conf_directory.*', 'exhibitor.name as topic');

		} elseif ($directory_type->sponsor_id <> 0) {

			$sort_order = request()['event']['sponsor_settings']['sortType'];

			$query->join('conf_event_sponsors as es', 'conf_directory.sponsor_id', '=', 'es.id')
				->leftJoin('conf_event_sponsor_categories AS sponsor_cat', function ($j) {
					$j->on('es.id', '=', 'sponsor_cat.sponsor_id')->whereNull('sponsor_cat.deleted_at');
				})
				->leftJoin('conf_event_categories AS ec', 'ec.id', '=', 'sponsor_cat.category_id');
			if ($sort_order == 1) {
				$query->orderByRaw('ISNULL(ec.sort_order),ec.sort_order ASC')->orderby('es.name', 'ASC');
			} else {
				$query->orderby('es.name', 'ASC');
			}

			$query->groupBy('conf_directory.sponsor_id')->select('conf_directory.*', 'es.name as topic');
			
		} else {
			$query->orderby('conf_directory.sort_order', 'ASC');
		}

		$directories = $query->get()->toArray();

		$new_directories = [];

		foreach ($directories as $key => $dir) {

			if(static::isAttendeeAttachWithDocument($dir,request()->attendee)){
				
				foreach ($dir['info'] as $item) {
					if ($dir['topic']) {
						$dir[$item['name']] = $dir['topic'];
						unset($dir['topic']);
					} else {
						$dir[$item['name']] = $item['value'];
					}
				}

				unset($dir['info']);

				foreach ($dir['files'] as $ikey => $file) {

					foreach ($file['info'] as $info) {
						$file[$info['name']] = $info['value'];
					}

					unset($file['info']);

					if ($file['s3'] == 1) {
						$file['s3_url'] = getS3Image('assets/directory/' . $file['path']);
					}

					$dir['files'][$ikey] = $file;
				}

				$children = $this->getBreadcrumb($dir['id'], $event_id, $lang_id);

				$new_array = array_values(Arr::sort(array_merge($children, $dir['files']), function ($value) {
					return $value['sort_order'];
				}));

				$new_directories[$key] = $dir;

				$new_directories[$key]['children'] = $children;

				$new_directories[$key]['children_files'] = $new_array;

			}

		}

		return $new_directories;
	}
	
	public static function isAttendeeAttachWithDocument($dir, $attendee_id)
	{
		if (count($dir['groups']) > 0) {
			if ($attendee_id == 0) {
				return false;
			} else {
				return static::isAttendeeAttachWithGroup($dir, $attendee_id);
			}
		} else {
			return true;
		}
	}
	public static function isAttendeeAttachWithGroup($dir, $attendee_id)
	{
		$assinged = false;
		foreach ($dir['groups'] as $key => $value) {
			$event_group = \App\Models\EventGroup::find($value['id']);
			if ($event_group->status == 1) {
				$group = \App\Models\EventAttendeeGroup::where('group_id', $value['id'])->where('attendee_id', $attendee_id)->get()->count();
				if ($group != 0) {
					$assinged = true;
				}
			}
		}
		return $assinged;
	}
	public function getListOfGroups($formInput)
	{
		$event_id = $formInput['event_id'];
		$data = [];
		$groups = EventGroup::where('event_id', '=', $event_id)
			->where('parent_id', '=', '0')
			->with(['info' => function ($query) use ($formInput) {
				return $query->where('languages_id', '=', $formInput['language_id'])->where('name', 'name')->select(['group_id', 'value']);
			}])
			->with(['children' => function ($r) {
				return $r->whereNull('deleted_at')->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
			}, 'children.childrenInfo' => function ($r) use ($formInput) {
				return $r->whereNull('deleted_at')->where('languages_id', '=', $formInput['language_id'])->where('name', 'name')
					->select(['group_id', 'value']);
			}])
			->whereNull('deleted_at')->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
		foreach ($groups as $k => $group) {
			$data[$k]['label'] = $group->info->value;
			foreach ($group->children as $key => $child) {
				$data[$k]['options'][$key] = array(
					'id' => $child->id,
					'value' => $child->id,
					'key' => $key,
					'label' => $child->info->value
				);
			}
		}
		return $data;
	}
}
