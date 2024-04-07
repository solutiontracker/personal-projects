<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Eventbuizz\Repositories\GeneralRepository;

use App\Eventbuizz\Repositories\EventSiteRepository;

use Illuminate\Http\Request;

class GeneralController extends Controller
{
    protected $generalRepository;

    /**
     * __construct
     *
     * @param  mixed $generalRepository
     * @return void
     */
    public function __construct(GeneralRepository $generalRepository)
    {
        $this->generalRepository = $generalRepository;
    }
        
    /**
     * getMetadata
     *
     * @param  mixed $request
     * @param  mixed $event_url
     * @param  mixed $param
     * @return void
     */
    public function getMetadata(Request $request, $event_url, $param)
    {
        $response = array();

        if($param == "custom_fields") {
            $response = EventSiteRepository::getCustomFields($request->all());
            return response()->json([
                'success' => true,
                'data' => [
                    $param => $response
                ]
            ]);
        } else if(in_array($param, ["languages", "countries", "country_codes", "timezones"])) {
            $response = $this->generalRepository->getMetadata($param, request()->event_id);
            return response()->json([
                'success' => true,
                'data' => $response
            ]);
        }
    }
}
