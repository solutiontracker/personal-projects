<?php
namespace App\Libraries\Sproom;

class SproomApi
{
    static public function sproomAPI($options)
    {
        $status_array = array(
            'error' => false,
            'error_message' => null,
            'response' => null,
        );

        $urls = array(
            'auth' => config("services.sproom.apiEndPoint").'/api/token',
            'upload' => config("services.sproom.apiEndPoint").'/api/documents',
        );

        $headers = array(
            "content-type: application/x-www-form-urlencoded",
        );

        $token =  config("services.sproom.apiKey");

        $headers = array(
            'content-type: application/octet-stream'
        );

        if(!file_exists($options['file_path']))
        {
            $status_array['error'] = true;
            $status_array['error_message'] = 'EAN XML file not found.';
            return $status_array;
        }

        $byte_array = file_get_contents($options['file_path']);

        $response = self::_call('POST', $urls['upload'], $byte_array, $headers, $token);
        
        return $response;
    }

    static private function _call($type = 'GET', $url, $params = array(), $headers = [], $token = null)
    {
        $headers_default = array(
            "accept: application/json",
            "cache-control: no-cache",
        );
        if ($token != '') {
            $headers_default[] = "Authorization: Bearer " . $token;
        }
        $headers = array_merge($headers_default, $headers);
        if (array_search('content-type: application/octet-stream', $headers) === false) {
            $params = http_build_query($params);
        }
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "$url",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_CUSTOMREQUEST => $type,
            CURLOPT_POSTFIELDS => $params,
            CURLOPT_HTTPHEADER => $headers,
        ));
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {
            return false;
        }

        return json_decode($response);
    }
}
