<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Eventbuizz\Repositories\DirectoryRepository;
use App\Http\Resources\EventsiteModuleCollection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventSiteDocumentController extends Controller
{
    public $successStatus = 200;

    protected $directoryRepository;

    public function __construct(DirectoryRepository $directoryRepository)
    {
        $this->directoryRepository = $directoryRepository;
    }

    public function index(Request $request, $slug)
    {
        $parentdirectories = $this->directoryRepository->getParentDirectories($request->all());
        return response()->json([
            'data' => [
                'documents' => $parentdirectories, 
            ],
        ]); 
    }
}
