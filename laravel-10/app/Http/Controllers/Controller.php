<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Eventbuizz\Repositories\EventSettingRepository;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    { }

    public function updateColumnStatus(Request $request)
    {
        EventSettingRepository::updateColumnStatus($request->all());

        return response()->json([
            'success' => true,
        ], 200);
    }
}
