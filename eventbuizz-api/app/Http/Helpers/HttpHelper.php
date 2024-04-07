<?php

namespace App\Http\Helpers;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class HttpHelper
{

    /**
     * success json response
     *
     * @param string $message
     * @param string $title
     * @param array|mixed $data
     * @param mixed $statusCode
     *
     * @return JsonResponse
     */
    public static function successJsonResponse($message, $title = 'Operation successful', $data = [], $statusCode = 200) {
        if (empty($data)) {
            return response()->json([
                    'success' => true,
                    'title' => $title,
                    'message' => $message,
                    'data' => $data
                ], $statusCode, [], JSON_FORCE_OBJECT
            );
        }
        return response()->json(['success' => true, 'title' => $title, 'message' => $message, 'data' => $data], $statusCode);
    }


    /**
     * error json response
     *
     * @param string $message
     * @param string $title
     * @param array|mixed $data
     * @param mixed $statusCode
     *
     * @return JsonResponse
     */
    public static function errorJsonResponse($message, $title = 'Can not perform operation', $data = [], $statusCode = 200) {
        if (empty($data)) {
            return response()->json(['success' => false, 'title' => $title, 'message' => $message, 'data' => $data], $statusCode, [], JSON_FORCE_OBJECT);
        }
        return response()->json(['success' => false, 'title' => $title, 'message' => $message, 'data' => $data], $statusCode);
    }



    /**
     * exception json response
     *
     * @param \Exception $exception
     * @param boolean $debug
     *
     * @return JsonResponse
     */
    public static function exceptionJsonResponse($exception, $debug = false) {
        $title = 'Something went wrong';
        $message = 'Something went wrong, please try again later';
        $data = [];
        if ($debug) {
            $data = ['file' => $exception->getFile(), 'line' => $exception->getLine(), 'message' => $exception->getMessage()];
            return self::errorJsonResponse($title, $message, $data);
        }
        return self::errorJsonResponse($title, $message, $data);
    }


    /**
     * simple error response
     *
     * @param string $message
     * @param array|mixed $data
     * @param string $title
     *
     * @return array
     */
    public static function errorResponse($message, $data = [], $title = 'Unsuccessful') {
        return [
            'success' => false,
            'message' => $message,
            'data' => $data,
            'title' => $title
        ];
    }


    /**
     * simple success response
     *
     * @param string $message
     * @param array|mixed $data
     * @param string $title
     *
     * @return array
     */
    public static function successResponse($message, $data = [], $title = 'Successful') {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'title' => $title
        ];
    }


    /**
     * pagination helper, returns processed paginated data
     *
     * @param LengthAwarePaginator $results
     *
     * @return array
     */
    public static function paginator($results) {
        return [
            "path" => $results->path(),
            "first_page_url" => $results->path().'?page=1',
            "last_page_url" => $results->path().'?page='.$results->lastPage(),
            "current_page_url" => $results->path().'?page='.$results->currentPage(),
            "previous_page_url" => $results->previousPageUrl(),
            "next_page_url" => $results->nextPageUrl(),
            "current_page" => $results->currentPage(),
            "total_records" => $results->total(),
            "per_page" => $results->perPage(),
            "current_page_records" => $results->count(),
            "next_page" => ($results->hasMorePages()) ? $results->currentPage()+1 : 0,
            "total_pages" => $results->lastPage(),
        ];
    }

}
