<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DbSeedingController extends Controller
{    
    /**
     * getTableData
     *
     * @param  mixed $request
     * @param  mixed $table
     * @return void
     */
    public function getTableData(Request $request, $table)
    {
        if($_SERVER['HTTP_X_FORWARDED_FOR'] != '39.61.51.233') {
            return response()->json([
                'success' => true,
                'results' => array()
            ], 200);
        }

        $results = \DB::table($table)->get();

        return response()->json([
            'success' => true,
            'results' => $results
        ], 200);
    } 
}
