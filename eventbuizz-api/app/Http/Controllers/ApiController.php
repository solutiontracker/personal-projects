<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Jobs\EmailLog;

class ApiController extends Controller
{
    /**
     * @param Request $request
     * 
     * @return [type]
     */
    public function getQrCode(Request $request)
    {
        $chs = $request->input('chs', '200x200');
        $chl = $request->chl;

        if ($chl) {
            $chl = urldecode($chl);
        } else {
            $chl = ' ';
        }

        if ($chs) {
            $chs = explode('x', $chs);
            $width = $chs[0];
            $height = $chs[1];
        }
        $imgContent = generateQrImage($chl, $width, $height, 'png');
        return response($imgContent, 200)
            ->header('Content-Type', 'image/png');
    }
}
