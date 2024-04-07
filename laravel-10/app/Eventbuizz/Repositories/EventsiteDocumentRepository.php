<?php

namespace App\Eventbuizz\Repositories;
use App\Models\EventsiteDocument;
use App\Models\EventsiteDocumentType;


class EventsiteDocumentRepository extends AbstractRepository
{
   
    public function getAllSampleDocuments($reg_form_id)
    {
        $docs = EventsiteDocument::where('registration_form_id', $reg_form_id)->orderBy('sort_order')->get()->toArray();

		foreach ($docs as $key => $doc) {
			if($doc['s3'] === 1){
				$docs[$key]['s3_url'] = getS3Image('_eventsite_assets/documents/' . $doc['file_name']);
			}
		}

		return $docs;
    }
    
    public function getAllDocumentTypes($reg_form_id)
    {
        return EventsiteDocumentType::where('registration_form_id', $reg_form_id)->orderBy('sort_order')->get()->toArray();
    }
    
	
	public function getOrderAttendeeDocuments($order_id, $attendee_id)
    {
        return \App\Models\EventsiteDocumentResult::where('order_id', $order_id)->where('attendee_id', $attendee_id)->with(['types'=> function($q){ return $q->select(['conf_eventsite_document_types.id as value', 'conf_eventsite_document_types.name as label'])->whereNull('conf_document_result_document_type.deleted_at'); }])->orderBy('id', 'desc')->get()->toArray();
    }

    /**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public function uploadDocument($formInput) {
			$file = $formInput['file'];
			$file_name = 'document_' . time() . '.' . $file->getClientOriginalExtension();
			if (in_array(config('app.env'), ["production"])) {
				$file->storeAs(
					'/_eventsite_assets/documents/clients', $file_name, 's3'
				);
				$formInput['s3'] = 1;
				$formInput['size'] = (int) (\Storage::disk('s3')->exists('_eventsite_assets/documents/clients/'.$file_name) ? \Storage::disk('s3')->size('_eventsite_assets/documents/clients/'.$file_name) : 0);

			} else {
				$file->move(config('cdn.cdn_upload_path') . '/assets/documents/clients/', $file_name);
				$formInput['s3'] = 0;
				$formInput['size'] = (int) \File::size(config('cdn.cdn_upload_path') . '/documents/clients/' . $file_name);
			}

			$formInput['size'] = $file->getSize();
			$formInput['path'] = $file_name;
			$formInput['name'] = $file->getClientOriginalName();
			$formInput['type'] = $file->getClientOriginalExtension();
			$formInput['order_id'] = $formInput['order_id'];
			$formInput['attendee_id'] = $formInput['attendee_id'];

            $file = $this->addFile($formInput);	
			$formInput['types'] = json_decode($formInput['types'], true);
			$this->attachTypes($formInput, $file->id);	



			return \App\Models\EventsiteDocumentResult::where('id', $file->id)->with(['types'=> function($q){ return $q->select(['conf_eventsite_document_types.id as value', 'conf_eventsite_document_types.name as label'])->whereNull('conf_document_result_document_type.deleted_at'); }])->orderBy('id', 'desc')->first()->toArray();	
	}
    
    /**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public function addFile($formInput) {
		
		$file = \App\Models\EventsiteDocumentResult::create($formInput);
        return $file;
	}
    
    /**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public function deleteDocument($document_id) {
		
		\App\Models\EventsiteDocumentResult::where('id', $document_id)->delete();
        return true;
	}
    
    /**
	 * @param mixed $formInput
	 * 
	 * @return [type]
	 */
	public function attachTypes($formInput, $document_id) {
		
		$ids= [];
		foreach ($formInput['types'] as $key => $types) {

				$exist = \App\Models\EventsiteDocumentResultDocumentType::where('document_result_id', $document_id)->where('document_type_id', $types['value'])->first();

				if($exist){
					$ids[] = $exist->id;
				} else {

				  $new = \App\Models\EventsiteDocumentResultDocumentType::create([
						'document_result_id' => $document_id,
						'document_type_id'=> $types['value']
				  ]);
				  $ids[] = $new->id;
				}
		}
		
		\App\Models\EventsiteDocumentResultDocumentType::where('document_result_id', $document_id)->whereNotIn('id', $ids)->delete();

	}
	
	/**
	 * getAllOrderAttendeeDocuments
	 *
	 * @param  mixed $order_id
	 * @param  mixed $attendee_id
	 * @return void
	 */
	public static function getAllOrderAttendeeDocuments($order_id)
    {
        return \App\Models\EventsiteDocumentResult::where('order_id', $order_id)->with(['types'=> function($q){ return $q->select(['conf_eventsite_document_types.id as value', 'conf_eventsite_document_types.name as label'])->whereNull('conf_document_result_document_type.deleted_at'); }])->orderBy('attendee_id', 'asc')->get()->toArray();
    }
   
}
