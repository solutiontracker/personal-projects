<?php
/*
 * General Controller hold all generic/common methods
 */

namespace App\Http\Controllers\Wizard;

use App\Http\Controllers\Controller;
use App\Eventbuizz\Repositories\GeneralRepository;
use App\Eventbuizz\Repositories\ImportRepository;
use App\Eventbuizz\Repositories\AttendeeRepository;
use App\Eventbuizz\Repositories\ProgramRepository;
use App\Http\Controllers\Wizard\Requests\UploadFileRequest;
use App\Http\Controllers\Wizard\Requests\ImportRequest;

class GeneralController extends Controller
{
    protected $generalRepository;

    protected $importRepository;

    private $attendeeRepository;

    private $programRepository;

    public function __construct(GeneralRepository $generalRepository, ImportRepository $importRepository, AttendeeRepository $attendeeRepository, ProgramRepository $programRepository)
    {
        $this->generalRepository = $generalRepository;
        $this->importRepository = $importRepository;
        $this->attendeeRepository = $attendeeRepository;
        $this->programRepository = $programRepository;
    }
    /*
     * get interface labels
     * @param $lang
     * @return mixed
     */
    public function getInterfaceLabels($lang = null)
    {
        $labels = $this->generalRepository->getGenericInterfaceLabels($lang);
        return response()->json([
            'success' => true,
            'message' => __('messages.fetch'),
            'data' => [
                'interface-labels' => $labels
            ]
        ]);
    }
    /*
     * get metadata
     * @param $param
     * @return mixed
     */
    public function getMetadata($param = null)
    {
        $headers = getallheaders();

        $event_id = (isset($headers['Event-Id']) && $headers['Event-Id'] ? $headers['Event-Id'] : null);

        $metadata = $this->generalRepository->getMetadata($param, $event_id);

        return response()->json([
            'success' => true,
            'message' => __('messages.fetch'),
            'data' => [
                'records' => $metadata
            ]
        ]);
    }

    /*
     * import
     * @param string
     * @return mixed
     */
    public function import(ImportRequest $request, $entity)
    {
        try {
            //Return message entity wise
            $message = "";

            //decode column
            $request->merge([
                'column' => (request()->has('column') ? json_decode(request()->get('column'), true) : [])
            ]);

            $delimeter = ($request->delimeter ? $request->delimeter : ';');

            if ($entity == "attendees") {
                $repository = $this->attendeeRepository;
                $importRecords = "import";
                $message = __('messages.import_record_create_update_success_message');
            } else if ($entity == "program") {
                $repository = $this->programRepository;
                $importRecords = "import";
            } else if ($entity == "attendee-invites") {
                $repository = $this->attendeeRepository;
                $importRecords = "importAttendeeInvitations";
            }

            //upload file
            $name = 'document_' . time() . '.' . $request->file->getClientOriginalExtension();
            $request->file->storeAs('assets/import', $name);

            $data = $this->importRepository->import(storage_path('app/assets/import/' . $name), $delimeter);
            $headers = array_shift($data);
            $mapping = array();

            foreach ($headers as $key => $header) {
                if (isset($request->column[$key])) {
                    $mapping[$key] = $request->column[$key];
                }
            }

            if (count($mapping) > 1) {
                $results = $repository->$importRecords($request->all(), $mapping, $data);

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'results' => $results
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('messages.import_invalid_csv_file'),
                ]);
            }
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => __('messages.import_invalid_csv_file'),
            ]);
        }
    }

    /*
     * upload file
     * @param $request
     * @return mixed
     */
    public function upload_file(UploadFileRequest $request)
    {
        $name = 'document_' . time() . '.' . $request->file->getClientOriginalExtension();
        $request->file->storeAs('assets/import', $name);
        return response()->json([
            'success' => true,
            'message' => __('messages.on_upload'),
            'data' => [
                'file_name' => $name,
                'storage' => storage_path('assets/import/' . $name),
            ]
        ]);
    }
}
