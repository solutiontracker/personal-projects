<?php
namespace App\Http\Controllers\RegistrationFlow;

use App\Eventbuizz\Repositories\EventsiteDocumentRepository;

use App\Eventbuizz\Repositories\EventSiteSettingRepository;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Http\Controllers\RegistrationFlow\Requests\DocumentRequest;

class ManageDocumentController extends Controller
{
    public $successStatus = 200;

    protected $eventSiteDocumentRepository;

    /**
     * @param EventsiteDocumentRepository $eventSiteDocumentRepository
     */
    public function __construct(EventsiteDocumentRepository $eventSiteDocumentRepository)
    {
        $this->eventSiteDocumentRepository = $eventSiteDocumentRepository;
    }

    public function getInitialDataByRegFormId(Request $request, $slug,  $order_id, $attendee_id)
    {
        $request->merge(["order_id" => $order_id, "attendee_id" => $attendee_id,]);

        $EBOrder = new \App\Eventbuizz\EBObject\EBOrder([], $request->order_id);

        $order_attendee = $EBOrder->_getAttendeeByID($request->attendee_id)->getOrderAttendee();

        $registration_form = $EBOrder->getRegistrationForm($attendee_id);

        $registration_form_id = $registration_form ? $registration_form->id : 0;

        $docs = $this->eventSiteDocumentRepository->getAllSampleDocuments($registration_form_id);

        $types = $this->eventSiteDocumentRepository->getAllDocumentTypes($registration_form_id);

        $order_attendee_docs = $this->eventSiteDocumentRepository->getOrderAttendeeDocuments($order_id,$attendee_id);

        return response()->json([
            'success' => true,
            'data' => array(
                "order" => $EBOrder->getModel(), 
                "docs" => $docs, 
                "types" => $types,
                "order_attendee_docs" => $order_attendee_docs,
            ),
        ], $this->successStatus);
    }

    /**
     * @param Request $request
     * @param mixed $module
     *
     * @return [type]
     */
    public function uploadDocument(DocumentRequest $request, $slug,  $order_id, $attendee_id)
    {
        $request->merge(["order_id" => $order_id, "attendee_id" => $attendee_id,]);

        $data = $this->eventSiteDocumentRepository->uploadDocument($request->all());

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => __('messages.create'),
        ], $this->successStatus)->header("Access-Control-Allow-Origin", $_SERVER['HTTP_ORIGIN']);

    }
    
    /**
     * @param Request $request
     * @param mixed $module
     *
     * @return [type]
     */
    public function deleteDocument(Request $request, $slug,  $document_id)
    {

        $data = $this->eventSiteDocumentRepository->deleteDocument($document_id);

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => __('messages.delete'),
        ], $this->successStatus)->header("Access-Control-Allow-Origin", $_SERVER['HTTP_ORIGIN']);

    }
    
    /**
     * @param Request $request
     * @param mixed $module
     *
     * @return [type]
     */
    public function attachTypes(Request $request, $slug,  $document_id)
    {

        $data = $this->eventSiteDocumentRepository->attachTypes($request->all(), $document_id);

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => __('messages.delete'),
        ], $this->successStatus)->header("Access-Control-Allow-Origin", $_SERVER['HTTP_ORIGIN']);

    }
    

}
