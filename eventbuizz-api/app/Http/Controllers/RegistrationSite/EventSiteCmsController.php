<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Eventbuizz\Repositories\PageBuilderPageRepository;
use App\Http\Resources\EventsiteModuleCollection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventSiteCmsController extends Controller
{
    public $successStatus = 200;

    protected $pageBuilderPageRepository;

    public function __construct(PageBuilderPageRepository $pageBuilderPageRepository)
    {
        $this->pageBuilderPageRepository = $pageBuilderPageRepository;
    }

    public function show($slug, $id)
    {
        $page = $this->pageBuilderPageRepository->getPageById($id);
        return response()->json([
            'data' => $page,
        ]);
    }
}
