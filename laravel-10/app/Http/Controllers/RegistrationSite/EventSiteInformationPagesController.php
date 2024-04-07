<?php

namespace App\Http\Controllers\RegistrationSite;

use App\Eventbuizz\Repositories\InformationPagesRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EventSiteInformationPagesController extends Controller
{
    public $successStatus = 200;

    protected $informationPagesRepository;

    public function __construct(InformationPagesRepository $informationPagesRepository)
    {
        $this->informationPagesRepository = $informationPagesRepository;
    }

    public function getInfoPage(Request $request, $slug, $id)
    {
        $page = $this->informationPagesRepository->getPageById($id, $request->all());
        return response()->json([
            'data' => $page,
        ]);
    }
}
