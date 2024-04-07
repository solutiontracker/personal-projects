<?php

namespace App\Http\Controllers\Wizard;

use App\Eventbuizz\Repositories\DirectoryRepository;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Wizard\Requests\Directory\DocumentRequest;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    protected $directoryRepository;

    public $successStatus = 200;

    /**
     * @param DirectoryRepository $directoryRepository
     */
    public function __construct(DirectoryRepository $directoryRepository)
    {
        $this->directoryRepository = $directoryRepository;
    }

    /**
     * @param Request $request
     * @param mixed $module
     * @param mixed $id
     *
     * @return [type]
     */
    public function listing(Request $request, $module, $id)
    {
        $request->merge(["module" => $module]);

        $data = $this->directoryRepository->listing($request->all(), $id);

        return response()->json([
            'success' => true,
            'data' => $data,
        ], $this->successStatus);
    }

    /**
     * @param DocumentRequest $request
     * @param mixed $module
     *
     * @return [type]
     */
    public function addDocument(DocumentRequest $request, $module)
    {
        $request->merge([
            "agenda_id" => $request->agenda_id ? $request->agenda_id : "0",
            "speaker_id" => $request->speaker_id ? $request->speaker_id : "0",
            "sponsor_id" => $request->sponsor_id ? $request->sponsor_id : "0",
            "exhibitor_id" => $request->exhibitor_id ? $request->exhibitor_id : "0"
        ]);

        $this->directoryRepository->addDocument($request->all());

        return response()->json([
            'success' => true,
            'message' => __('messages.create'),
        ], $this->successStatus);
    }

    /**
     * @param DocumentRequest $request
     * @param mixed $module
     *
     * @return [type]
     */
    public function updateDocument(DocumentRequest $request, $module)
    {
        $this->directoryRepository->updateDocument($request->all());

        return response()->json([
            'success' => true,
            'message' => __('messages.update'),
        ], $this->successStatus);
    }

    /**
     * @param Request $request
     * @param mixed $module
     * @param mixed $id
     *
     * @return [type]
     */
    public function destroyDocument(Request $request, $module)
    {
        if ($request->ids) {
            foreach ($request->ids as $id) {
                $parts = explode("-", $id);
                if (count($parts) == 2) {
                    if ($parts[0] == "file") {
                        $this->directoryRepository->destroyDocumentFile($parts[1]);
                    } else if ($parts[0] == "folder") {
                        $rows = $this->directoryRepository->listing($request->all(), [], $parts[1]);
                        foreach ($rows as $row) {
                            if ($row['type'] == "file") {
                                $this->directoryRepository->destroyDocumentFile($row['id']);
                            } else if ($row['type'] == "folder") {
                                $this->directoryRepository->destroyDocument($row['id']);
                            }
                        }
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => __('messages.delete'),
        ], $this->successStatus);
    }

    /**
     * @param Request $request
     * @param mixed $module
     *
     * @return [type]
     */
    public function uploadDocument(DocumentRequest $request, $module)
    {
        $this->directoryRepository->uploadDocument($request->all());

        return response()->json([
            'success' => true,
            'message' => __('messages.create'),
        ], $this->successStatus);
    }

    /**
     * @param Request $request
     * @param mixed $module
     *
     * @return [type]
     */
    public function renameDocumentFile(DocumentRequest $request, $module)
    {
        $this->directoryRepository->renameDocumentFile($request->all());

        return response()->json([
            'success' => true,
            'message' => __('messages.update'),
        ], $this->successStatus);
    }

    /**
     * @param Request $request
     * @param mixed $module
     *
     * @return [type]
     */
    public function scheduleDocument(DocumentRequest $request, $module)
    {
        $this->directoryRepository->scheduleDocument($request->all());

        return response()->json([
            'success' => true,
            'message' => __('messages.update'),
        ], $this->successStatus);
    }

    /**
     * @param Request $request
     * @param mixed $module
     *
     * @return [type]
     */
    public function moveFile(Request $request, $module)
    {
        $this->directoryRepository->moveFile($request->all());

        return response()->json([
            'success' => true,
            'message' => __('messages.update'),
        ], $this->successStatus);
    }

    /**
     * @param Request $request
     * @param mixed $module
     *
     * @return [type]
     */
    public function copyFile(Request $request, $module)
    {
        $this->directoryRepository->copyFile($request->all());

        return response()->json([
            'success' => true,
            'message' => __('messages.update'),
        ], $this->successStatus);
    }

    /**
     * @param Request $request
     * @param mixed $module
     *
     * @return [type]
     */
    public function loadModuleData(Request $request, $module)
    {
        if ($module == "agendas") {
            $data = $this->directoryRepository->getPrograms($request->all());
        } else if ($module == "speakers") {
            $data = $this->directoryRepository->getSpeakers($request->all());
        } else if ($module == "sponsors") {
            $data = $this->directoryRepository->getSponsors($request->all());
        } else if ($module == "exhibitors") {
            $data = $this->directoryRepository->getExhibitors($request->all());
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ], $this->successStatus);
    }


    /**
     * @param Request $request
     * @param mixed $module
     * @param mixed $id
     * 
     * @return [type]
     */
    public function downloadFile(Request $request, $module, $id)
    {
        $file = $this->directoryRepository->getFile($request->all(), $id);
        
        $downloadLink = config('app.eventcenter_url').'/assets/directory/'.$file->path;

        header('Content-Type: '.getContentTypeByExtension(end(explode('.',$file->path))));

        header("Content-disposition:attachment; filename=".$file->path);

        readfile($downloadLink);

        exit;
    }
    /**
     * @param Request $request
     *
     * @return [type]
     */
    public function loadGroupData(Request $request)
    {
        $groups = $this->directoryRepository->getListOfGroups($request->all());
        return response()->json([
            'success' => true,
            'data' => $groups,
        ], $this->successStatus);
    }
}
