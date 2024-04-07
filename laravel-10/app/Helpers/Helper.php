<?php

use Illuminate\Support\Str;

function myIp($ip) {

    if(in_array($ip, ['39.61.51.233', '203.128.11.65', '182.187.142.170', '213.32.244.105', '94.18.200.102', '3.74.98.181','85.204.195.111', '182.176.83.18'])) { //85.204.195.111 - Louise
        return true;
    }

    return false;
}

function print_all($data)
{
    if(myIp($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        echo '<pre>';
        print_r($data);
        exit;
    }
}

function array_change_key_value($array, $old_key, $new_key)
{
    if (!array_key_exists($old_key, $array))
        return $array;
    $keys = array_keys($array);
    $keys[array_search($old_key, $keys)] = $new_key;
    return array_combine($keys, $array);
}

function organizer_id()
{
    $user = request()->user();
    if($user) {
        if ($user->user_type == 'super') {
            $organizer_id = $user->id;
        } elseif ($user->user_type == 'demo') {
            $organizer_id = $user->id;
        } else {
            $organizer_id = $user->parent_id;
        }
        return $organizer_id;
    } else {
        return request()->organizer_id;
    }
}

function super_organizer_id($user = null)
{
    if(!$user) {
        $user = request()->user();
        if(!$user) {
            $user = \App\Models\Organizer::where('id', request()->organizer_id)->first();
        }
    }
    
    return $user->id;
}

function organizer_info()
{
    if(request()->user()) {
        return request()->user();
    } else {
        return \App\Models\Organizer::where('id', request()->organizer_id)->first();
    }
}

function trim_data($data)
{
    if ($data == null)
        return '';

    if (is_array($data)) {
        return array_map('trim_data', $data);
    } else return trim($data);
}


function  get_trim_all_data($formInput, $entity = '')
{
    if (in_array($entity, ['attendee', 'speaker', 'sponsor', 'exhibitor'])) {
        $trimmed_array = trim_data($formInput);
        return $trimmed_array;
    } else {
        return $formInput;
    }
}

function readArrayKey($row, $array, $key)
{
    foreach ($row[$key] as $val) {
        $array[$val['name']] = $val['value'];
    }
    return $array;
}

function convertToAssociativeArray($array){

    if(empty($array)){
        return;
    }

    $associativeArray = [];

    foreach ($array as $index => $key){
        $associativeArray[$key["name"]] = $key["value"];
    }

    return $associativeArray;
}

function readArrayIndex($array, $index)
{
    if(isset($array[$index])) {
        return $array[$index];
    }

    return;
}

function returnArrayKeys($array, $keys) {
    $final_array = array();
    if(count($array) > 0) {
        foreach($array as $record) {
            $temp_array = array();
            foreach($record as $key => $rec) {
                if(in_array($key, $keys) && is_array($rec)) {
                    foreach($record[$key] as $info) {
                        $temp_array[$info['name']] = $info['value'];
                    }
                } else {
                    $temp_array[$key] = $rec;
                }

            }
            $final_array[] = $temp_array;
        }
    } else {
        return $array;
    }
    return $final_array;
}

function verifyDate($date)
{
    return (DateTime::createFromFormat('d-m-Y', $date) !== false);
}

function verifyTime($date)
{
    if ((DateTime::createFromFormat('H:i', $date) !== false) || (DateTime::createFromFormat('H:i:s', $date) !== false) || (DateTime::createFromFormat('H.i', $date) !== false) || (DateTime::createFromFormat('H.i.s', $date) !== false)) {
        return false;
    } else
        return true;
}

function set_error_delimeter($errors)
{
    $array = array();
    if (count($errors) > 0) {
        foreach ($errors as $error) {
            array_push($array, $error);
        }
    }
    return $array;
}

function cdn($url = null)
{
    $url = (string) $url;
    if (empty($url)) {
        throw new Exception('URL missing');
    }

    $pattern = '|^http[s]{0,1}://|i';
    if (preg_match($pattern, $url)) {
        throw new Exception(
            'Invalid URL. ' .
                'Use: /image.jpeg instead of full URI: ' .
                'http://domain.com/image.jpeg.'
        );
    }

    $pattern = '|^/|';
    if (!preg_match($pattern, $url)) {
        $url = '/' . $url;
    }

    if (!config('cdn.cdn_enabled')) {
        return $url;
    } else {
        return config('cdn.cdn_protocol') . '://' . config('cdn.cdn_domain') . $url;
    }
}

function days($from, $to)
{
    $from = \Carbon\Carbon::parse(\Carbon\Carbon::parse($from)->toDateString());
    return $days = $from->diffInDays(\Carbon\Carbon::parse(\Carbon\Carbon::parse($to)->toDateString()), false);
}

function is_base64($string)
{
    return Str::contains($string, 'base64');
}

function fetchImageName($url)
{
    $pathinfo = pathinfo($url);
    return $pathinfo['filename'] . '.' . $pathinfo['extension'];
}

function getCurrency($floatcurr, $curr = "USD")
{
    $currencies['ARS'] = array(2, ',', '.');          //  Argentine Peso
    $currencies['AMD'] = array(2, '.', ',');          //  Armenian Dram
    $currencies['AWG'] = array(2, '.', ',');          //  Aruban Guilder
    $currencies['AUD'] = array(2, '.', ' ');          //  Australian Dollar
    $currencies['BSD'] = array(2, '.', ',');          //  Bahamian Dollar
    $currencies['BHD'] = array(3, '.', ',');          //  Bahraini Dinar
    $currencies['BDT'] = array(2, '.', ',');          //  Bangladesh, Taka
    $currencies['BZD'] = array(2, '.', ',');          //  Belize Dollar
    $currencies['BMD'] = array(2, '.', ',');          //  Bermudian Dollar
    $currencies['BOB'] = array(2, '.', ',');          //  Bolivia, Boliviano
    $currencies['BAM'] = array(2, '.', ',');          //  Bosnia and Herzegovina, Convertible Marks
    $currencies['BWP'] = array(2, '.', ',');          //  Botswana, Pula
    $currencies['BRL'] = array(2, ',', '.');          //  Brazilian Real
    $currencies['BND'] = array(2, '.', ',');          //  Brunei Dollar
    $currencies['CAD'] = array(2, '.', ',');          //  Canadian Dollar
    $currencies['KYD'] = array(2, '.', ',');          //  Cayman Islands Dollar
    $currencies['CLP'] = array(0, '', '.');           //  Chilean Peso
    $currencies['CNY'] = array(2, '.', ',');          //  China Yuan Renminbi
    $currencies['COP'] = array(2, ',', '.');          //  Colombian Peso
    $currencies['CRC'] = array(2, ',', '.');          //  Costa Rican Colon
    $currencies['HRK'] = array(2, ',', '.');          //  Croatian Kuna
    $currencies['CUC'] = array(2, '.', ',');          //  Cuban Convertible Peso
    $currencies['CUP'] = array(2, '.', ',');          //  Cuban Peso
    $currencies['CYP'] = array(2, '.', ',');          //  Cyprus Pound
    $currencies['CZK'] = array(2, '.', ',');          //  Czech Koruna
    $currencies['DKK'] = array(2, ',', '.');          //  Danish Krone
    $currencies['DOP'] = array(2, '.', ',');          //  Dominican Peso
    $currencies['XCD'] = array(2, '.', ',');          //  East Caribbean Dollar
    $currencies['EGP'] = array(2, '.', ',');          //  Egyptian Pound
    $currencies['SVC'] = array(2, '.', ',');          //  El Salvador Colon
    $currencies['ATS'] = array(2, ',', '.');          //  Euro
    $currencies['BEF'] = array(2, ',', '.');          //  Euro
    $currencies['DEM'] = array(2, ',', '.');          //  Euro
    $currencies['EEK'] = array(2, ',', '.');          //  Euro
    $currencies['ESP'] = array(2, ',', '.');          //  Euro
    $currencies['EUR'] = array(2, ',', '.');          //  Euro
    $currencies['FIM'] = array(2, ',', '.');          //  Euro
    $currencies['FRF'] = array(2, ',', '.');          //  Euro
    $currencies['GRD'] = array(2, ',', '.');          //  Euro
    $currencies['IEP'] = array(2, ',', '.');          //  Euro
    $currencies['ITL'] = array(2, ',', '.');          //  Euro
    $currencies['LUF'] = array(2, ',', '.');          //  Euro
    $currencies['NLG'] = array(2, ',', '.');          //  Euro
    $currencies['PTE'] = array(2, ',', '.');          //  Euro
    $currencies['GHC'] = array(2, '.', ',');          //  Ghana, Cedi
    $currencies['GIP'] = array(2, '.', ',');          //  Gibraltar Pound
    $currencies['GTQ'] = array(2, '.', ',');          //  Guatemala, Quetzal
    $currencies['HNL'] = array(2, '.', ',');          //  Honduras, Lempira
    $currencies['HKD'] = array(2, '.', ',');          //  Hong Kong Dollar
    $currencies['HUF'] = array(0, '', '.');           //  Hungary, Forint
    $currencies['ISK'] = array(0, '', '.');           //  Iceland Krona
    $currencies['INR'] = array(2, '.', ',');          //  Indian Rupee
    $currencies['IDR'] = array(2, ',', '.');          //  Indonesia, Rupiah
    $currencies['IRR'] = array(2, '.', ',');          //  Iranian Rial
    $currencies['JMD'] = array(2, '.', ',');          //  Jamaican Dollar
    $currencies['JPY'] = array(0, '', ',');           //  Japan, Yen
    $currencies['JOD'] = array(3, '.', ',');          //  Jordanian Dinar
    $currencies['KES'] = array(2, '.', ',');          //  Kenyan Shilling
    $currencies['KWD'] = array(3, '.', ',');          //  Kuwaiti Dinar
    $currencies['LVL'] = array(2, '.', ',');          //  Latvian Lats
    $currencies['LBP'] = array(0, '', ' ');           //  Lebanese Pound
    $currencies['LTL'] = array(2, ',', ' ');          //  Lithuanian Litas
    $currencies['MKD'] = array(2, '.', ',');          //  Macedonia, Denar
    $currencies['MYR'] = array(2, '.', ',');          //  Malaysian Ringgit
    $currencies['MTL'] = array(2, '.', ',');          //  Maltese Lira
    $currencies['MUR'] = array(0, '', ',');           //  Mauritius Rupee
    $currencies['MXN'] = array(2, '.', ',');          //  Mexican Peso
    $currencies['MZM'] = array(2, ',', '.');          //  Mozambique Metical
    $currencies['NPR'] = array(2, '.', ',');          //  Nepalese Rupee
    $currencies['ANG'] = array(2, '.', ',');          //  Netherlands Antillian Guilder
    $currencies['ILS'] = array(2, '.', ',');          //  New Israeli Shekel
    $currencies['TRY'] = array(2, '.', ',');          //  New Turkish Lira
    $currencies['NZD'] = array(2, '.', ',');          //  New Zealand Dollar
    $currencies['NOK'] = array(2, ',', '.');          //  Norwegian Krone
    $currencies['PKR'] = array(2, '.', ',');          //  Pakistan Rupee
    $currencies['PEN'] = array(2, '.', ',');          //  Peru, Nuevo Sol
    $currencies['UYU'] = array(2, ',', '.');          //  Peso Uruguayo
    $currencies['PHP'] = array(2, '.', ',');          //  Philippine Peso
    $currencies['PLN'] = array(2, '.', ' ');          //  Poland, Zloty
    $currencies['GBP'] = array(2, '.', ',');          //  Pound Sterling
    $currencies['OMR'] = array(3, '.', ',');          //  Rial Omani
    $currencies['RON'] = array(2, ',', '.');          //  Romania, New Leu
    $currencies['ROL'] = array(2, ',', '.');          //  Romania, Old Leu
    $currencies['RUB'] = array(2, ',', '.');          //  Russian Ruble
    $currencies['SAR'] = array(2, '.', ',');          //  Saudi Riyal
    $currencies['SGD'] = array(2, '.', ',');          //  Singapore Dollar
    $currencies['SKK'] = array(2, ',', ' ');          //  Slovak Koruna
    $currencies['SIT'] = array(2, ',', '.');          //  Slovenia, Tolar
    $currencies['ZAR'] = array(2, '.', ' ');          //  South Africa, Rand
    $currencies['KRW'] = array(0, '', ',');           //  South Korea, Won
    $currencies['SZL'] = array(2, '.', ', ');         //  Swaziland, Lilangeni
    $currencies['SEK'] = array(2, ',', '.');          //  Swedish Krona
    $currencies['CHF'] = array(2, '.', '\'');         //  Swiss Franc
    $currencies['TZS'] = array(2, '.', ',');          //  Tanzanian Shilling
    $currencies['THB'] = array(2, '.', ',');          //  Thailand, Baht
    $currencies['TOP'] = array(2, '.', ',');          //  Tonga, Paanga
    $currencies['AED'] = array(2, '.', ',');          //  UAE Dirham
    $currencies['UAH'] = array(2, ',', ' ');          //  Ukraine, Hryvnia
    $currencies['USD'] = array(2, '.', ',');          //  US Dollar
    $currencies['VUV'] = array(0, '', ',');           //  Vanuatu, Vatu
    $currencies['VEF'] = array(2, ',', '.');          //  Venezuela Bolivares Fuertes
    $currencies['VEB'] = array(2, ',', '.');          //  Venezuela, Bolivar
    $currencies['VND'] = array(0, '', '.');           //  Viet Nam, Dong
    $currencies['ZWD'] = array(2, '.', ' ');          //  Zimbabwe Dollar

    if (!function_exists('formatinr')) {
        function formatinr($input)
        {
            //CUSTOM FUNCTION TO GENERATE ##,##,###.##
            $dec = "";
            $pos = strpos($input, ".");
            if ($pos === false) {
                //no decimals
            } else {
                //decimals
                $dec = substr(round(substr($input, $pos), 2), 1);
                $input = substr($input, 0, $pos);
            }
            $num = substr($input, -3); //get the last 3 digits
            $input = substr($input, 0, -3); //omit the last 3 digits already stored in $num
            while (strlen($input) > 0) //loop the process - further get digits 2 by 2
            {
                $num = substr($input, -2) . "," . $num;
                $input = substr($input, 0, -2);
            }
            return $num . $dec;
        }
    }


    if ($curr == "INR") {
        return formatinr((float)$floatcurr);
    } else {
        return number_format((float)$floatcurr, $currencies[$curr][0], $currencies[$curr][1], $currencies[$curr][2]);
    }
}

function getCurrencyArray()
{
    return array('208' => 'DKK', '978' => 'EUR', '840' => 'USD', '578' => 'NOK', '752' => 'SEK', '036' => 'AUD', '756' => 'CHF', '36' => 'AUD', '826' => 'GBP');
}

function getDatesFromRange($date_time_from, $date_time_to)
{
    // cut hours, because not getting last day when hours of time to is less than hours of time_from
    // see while loop
    $start = \Carbon\Carbon::createFromFormat('Y-m-d', substr($date_time_from, 0, 10));
    $end = \Carbon\Carbon::createFromFormat('Y-m-d', substr($date_time_to, 0, 10));
    $dates = [];
    while ($start->lte($end)) {
        $dates[] = $start->copy()->format('m/d/Y');
        $start->addDay();
    }
    return $dates;
}

function set_lang($date)
{
    $a_find_months = array(
        'January',
        'Jan',
        'February',
        'Feb',
        'March',
        'Mar',
        'April',
        'May',
        'June',
        'Jun',
        'July',
        'Jul',
        'August',
        'Aug',
        'September',
        'Sep',
        'October',
        'Oct',
        'November',
        'Nov',
        'December',
        'Dec',
    );

    $a_translations_months = array(
        'January' => __('wizard.datetime.DT_JANUARY'),
        'Jan' => __('wizard.datetime.DT_JANUARY'),
        'February' => __('wizard.datetime.DT_FEBUARY'),
        'Feb' => __('wizard.datetime.DT_FEBUARY'),
        'March' => __('wizard.datetime.DT_MARCH'),
        'Mar' => __('wizard.datetime.DT_MARCH'),
        'April' => __('wizard.datetime.DT_APRIL'),
        'May' => __('wizard.datetime.DT_MAY'),
        'June' => __('wizard.datetime.DT_JUN'),
        'Jun' => __('wizard.datetime.DT_JUN'),
        'July' => __('wizard.datetime.DT_JULY'),
        'Jul' => __('wizard.datetime.DT_JULY'),
        'August' => __('wizard.datetime.DT_AUGUST'),
        'Aug' => __('wizard.datetime.DT_AUGUST'),
        'September' => __('wizard.datetime.DT_SEPTEMBER'),
        'Sep' => __('wizard.datetime.DT_SEPTEMBER'),
        'October' => __('wizard.datetime.DT_OCTOBER'),
        'Oct' => __('wizard.datetime.DT_OCTOBER'),
        'November' => __('wizard.datetime.DT_NOVEMBER'),
        'Nov' => __('wizard.datetime.DT_NOVEMBER'),
        'December' => __('wizard.datetime.DT_DECEMBER'),
        'Dec' => __('wizard.datetime.DT_DECEMBER'),
    );

    $date = str_replace($a_find_months, $a_translations_months, $date);

    $a_find_time = array(
        'pm' => 'PM',
        'am' => 'AM',
    );

    $a_translations_time = array(
        'PM' => __('wizard.datetime.DT_PM'),
        'AM' => __('wizard.datetime.DT_AM')
    );

    $date = str_replace($a_find_time, $a_translations_time, $date);

    $a_find_days = array(
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday'
    );

    $a_translations_days = array(
        'Sunday' => __('wizard.datetime.DT_SUNDAY'),
        'Monday' => __('wizard.datetime.DT_MONDAY'),
        'Tuesday' => __('wizard.datetime.DT_TUESDAY'),
        'Wednesday' => __('wizard.datetime.DT_WEDNESDAY'),
        'Thursday' => __('wizard.datetime.DT_THURSDAY'),
        'Friday' => __('wizard.datetime.DT_FRIDAY'),
        'Saturday' => __('wizard.datetime.DT_SATURDAY')
    );

    $date = str_replace($a_find_days, $a_translations_days, $date);

    return $date;
}

function group_by($key, $data) {
    $result = array();
    foreach($data as $val) {
        if(array_key_exists($key, $val)){
            $result[$val[$key]][] = $val;
        }else{
            $result[""][] = $val;
        }
    }
    return $result;
}

function get_url_hostname($url)
{
    $parse = parse_url($url);
    return str_ireplace('www.', '', $parse['host']);
}

/**
 * @param mixed $video
 * @param bool $url
 * @param null $event_url
 * 
 * @return [type]
 */
function parse_video($video, $event_url = null, $attendee_id = null, $attachedAttendees = 0, $event_setting = array())
{
    if(in_array($video['type'], ["link", "live"])) {
        $url = $video['url'];
        $host = get_url_hostname($url);
        if(str_contains($url, "zoom.us")) {
            $query = parse_url($url, PHP_URL_QUERY);
            parse_str($query, $query_params);
            $password = (isset( $query_params['pwd']) &&  $query_params['pwd'] ? $query_params['pwd']: 0);
            $meeting_number = end(explode("/", parse_url($url, PHP_URL_PATH))) ;
            return array( 
                "host" => $host,
                "url" => config('app.url').'/mobile/event/'.$event_url.'/zoom/join/'.$attendee_id.'/'. $meeting_number.'/'. $password.'/1',
                "iframe" => 1
            );
        } else if($video['is_iframe'] == 1) {
            return array(
                "host" => $host,
                "url" => $url,
                "iframe" => 1
            );
        } else if(str_contains($url, "player.vimeo.com")) {
            return array(
                "host" => $host,
                "url" => $url,
                "iframe" => 1
            );
        } else {
            $response = LaravelMediaEmbed::parse($url);
            if($response) {
                $info = $response->stub();
                return array(
                    "host" => $host,
                    "url" => $info['iframe-player'],
                    "iframe" => 1
                );
            } else {
                $embed = Embed::make($url)->parseUrl();
                if($embed) {
                    try {
                        $iframe = $embed->getHtml();
                        preg_match('/src="([^"]+)"/', $iframe, $match);
                        return array(
                            "host" => $host,
                            "url" => $match[1],
                            "iframe" => 1
                        );
                    } catch (Exception $e) {
                        return array(
                            "host" => $host,
                            "url" => $url,
                            "iframe" => 0
                        );
                    }
                } else {
                    return array(
                        "host" => $host,
                        "url" => $url,
                        "iframe" => 0
                    );
                }
            }
        }
    } else if(in_array($video['type'], ['agora-realtime-broadcasting', 'agora-external-streaming', 'agora-rooms', 'agora-webinar', 'agora-panel-disscussions'])) {
        $service = (isset($event_setting['streaming_service']) && $event_setting['streaming_service'] == "vonage" ? "vonage" : "agora");

        if(in_array($video['type'], ['agora-panel-disscussions'])) {
            if($attachedAttendees > 0) {
                $url = config('app.virtual_app_url').'/event/'.$event_url.'/'.$service.'/join-video-meeting/'.$video['id'].'/Eventbuizz-'.$video['id'].'/'.($attendee_id == $video['moderator'] ? 'host' : 'participant').'/1';
            } else {
                $url = config('app.virtual_app_url').'/event/'.$event_url.'/'.$service.'/join-video-meeting/'.$video['id'].'/Eventbuizz-'.$video['id'].'/audience/1';
            }
        } else if(in_array($video['type'], ['agora-realtime-broadcasting', 'agora-external-streaming', 'agora-webinar'])) {
            $url = config('app.virtual_app_url').'/event/'.$event_url.'/'.$service.'/join-video-meeting/'.$video['id'].'/Eventbuizz-'.$video['id'].'/audience/1';
        } else {
            $url = '/event/'.$event_url.'/'.$service.'/join-video-meeting/'.$video['id'].'/Eventbuizz-'.$video['id'].'/'.($attendee_id == $video['moderator'] ? 'host' : 'participant');
        }
        return array( 
            "host" => config('app.virtual_app_url'),
            "url" => $url,
            "iframe" => 1
        );
    } else if(in_array($video['type'], ['agora-realtime-broadcasting-custom'])) {
        return array(
            "url" => url('/mobile/program/video/ivs/stream/'.$video['agenda_id'].'/'.$video['id']),
            "iframe" => 1
        );
    } else if($video['type'] == "local") {
        return array(
            "url" => url('/mobile/program/video/on-demand-stream/'.$video['agenda_id'].'/'.$video['id']),
            "iframe" => 1
        );
    }
}

/**
 * @param mixed $allDates
 * @param mixed $date
 * 
 * @return [type]
 */
function getClosestNextDate($allDates, $date) {
    function date_sort($a, $b) {
        return strtotime($a) - strtotime($b);
    }
    usort($allDates, "date_sort");
    foreach ($allDates as $count => $dateSingle) {
        if (strtotime($date) <= strtotime($dateSingle))  {
            $nextDate = date('Y-m-d', strtotime($dateSingle));
            break;
        }
    }
    return $nextDate;
}

/**
 * @param mixed $email
 * @param string $mask_char
 * @param mixed $percent=50
 * 
 * @return [type]
 */
function maskEmail( $email, $mask_char = '***', $percent=50 )
{
    list( $user, $domain ) = preg_split("/@/", $email );
    $len = strlen( $user );
    $mask_count = floor( $len * $percent /100 );
    $offset = floor( ( $len - $mask_count ) / 2 );
    $masked = substr( $user, 0, $offset )
            .str_repeat( $mask_char, $mask_count )
            .substr( $user, $mask_count+$offset );

    return( $masked.'@'.$domain );
}

/**
 * @param mixed $number
 * 
 * @return [type]
 */
function maskPhoneNumber($number)
{
    $mask_number =  str_repeat("*", strlen($number) - 4) . substr($number, -2);
    return $mask_number;
}

/**
 * @param mixed $format
 * @param mixed $date
 * 
 * @return [type]
 */
function getFormatDate($format, $date)
{
    $result = trim(strftime($format, strtotime($date)));

    return $result;
}

/**
 * @param mixed $api_key
 * @param mixed $api_secret
 * @param mixed $meeting_number
 * @param mixed $role
 * 
 * @return [type]
 */
function generate_zoom_signature( $api_key, $api_secret, $meeting_number, $role){

	$time = time() * 1000 - 30000;//time in milliseconds (or close enough)

	$data = base64_encode($api_key . $meeting_number . $time . $role);

	$hash = hash_hmac('sha256', $data, $api_secret, true);

	$_sig = $api_key . "." . $meeting_number . "." . $time . "." . $role . "." . base64_encode($hash);

	//return signature, url safe base64 encoded
	return rtrim(strtr(base64_encode($_sig), '+/', '-_'), '=');
}

function getContentTypeByExtension($ext)
{
    $types = array(
        'ai'      => 'application/postscript',
        'aif'     => 'audio/x-aiff',
        'aifc'    => 'audio/x-aiff',
        'aiff'    => 'audio/x-aiff',
        'asc'     => 'text/plain',
        'atom'    => 'application/atom+xml',
        'atom'    => 'application/atom+xml',
        'au'      => 'audio/basic',
        'avi'     => 'video/x-msvideo',
        'bcpio'   => 'application/x-bcpio',
        'bin'     => 'application/octet-stream',
        'bmp'     => 'image/bmp',
        'cdf'     => 'application/x-netcdf',
        'cgm'     => 'image/cgm',
        'class'   => 'application/octet-stream',
        'cpio'    => 'application/x-cpio',
        'cpt'     => 'application/mac-compactpro',
        'csh'     => 'application/x-csh',
        'css'     => 'text/css',
        'csv'     => 'text/csv',
        'dcr'     => 'application/x-director',
        'dir'     => 'application/x-director',
        'djv'     => 'image/vnd.djvu',
        'djvu'    => 'image/vnd.djvu',
        'dll'     => 'application/octet-stream',
        'dmg'     => 'application/octet-stream',
        'dms'     => 'application/octet-stream',
        'doc'     => 'application/msword',
        'dtd'     => 'application/xml-dtd',
        'dvi'     => 'application/x-dvi',
        'dxr'     => 'application/x-director',
        'eps'     => 'application/postscript',
        'etx'     => 'text/x-setext',
        'exe'     => 'application/octet-stream',
        'ez'      => 'application/andrew-inset',
        'gif'     => 'image/gif',
        'gram'    => 'application/srgs',
        'grxml'   => 'application/srgs+xml',
        'gtar'    => 'application/x-gtar',
        'hdf'     => 'application/x-hdf',
        'hqx'     => 'application/mac-binhex40',
        'htm'     => 'text/html',
        'html'    => 'text/html',
        'ice'     => 'x-conference/x-cooltalk',
        'ico'     => 'image/x-icon',
        'ics'     => 'text/calendar',
        'ief'     => 'image/ief',
        'ifb'     => 'text/calendar',
        'iges'    => 'model/iges',
        'igs'     => 'model/iges',
        'jpe'     => 'image/jpeg',
        'jpeg'    => 'image/jpeg',
        'jpg'     => 'image/jpeg',
        'js'      => 'application/x-javascript',
        'json'    => 'application/json',
        'kar'     => 'audio/midi',
        'latex'   => 'application/x-latex',
        'lha'     => 'application/octet-stream',
        'lzh'     => 'application/octet-stream',
        'm3u'     => 'audio/x-mpegurl',
        'man'     => 'application/x-troff-man',
        'mathml'  => 'application/mathml+xml',
        'me'      => 'application/x-troff-me',
        'mesh'    => 'model/mesh',
        'mid'     => 'audio/midi',
        'midi'    => 'audio/midi',
        'mif'     => 'application/vnd.mif',
        'mov'     => 'video/quicktime',
        'movie'   => 'video/x-sgi-movie',
        'mp2'     => 'audio/mpeg',
        'mp3'     => 'audio/mpeg',
        'mpe'     => 'video/mpeg',
        'mpeg'    => 'video/mpeg',
        'mpg'     => 'video/mpeg',
        'mpga'    => 'audio/mpeg',
        'ms'      => 'application/x-troff-ms',
        'msh'     => 'model/mesh',
        'mxu'     => 'video/vnd.mpegurl',
        'nc'      => 'application/x-netcdf',
        'oda'     => 'application/oda',
        'ogg'     => 'application/ogg',
        'pbm'     => 'image/x-portable-bitmap',
        'pdb'     => 'chemical/x-pdb',
        'pdf'     => 'application/pdf',
        'pgm'     => 'image/x-portable-graymap',
        'pgn'     => 'application/x-chess-pgn',
        'png'     => 'image/png',
        'pnm'     => 'image/x-portable-anymap',
        'ppm'     => 'image/x-portable-pixmap',
        'ppt'     => 'application/vnd.ms-powerpoint',
        'ps'      => 'application/postscript',
        'qt'      => 'video/quicktime',
        'ra'      => 'audio/x-pn-realaudio',
        'ram'     => 'audio/x-pn-realaudio',
        'ras'     => 'image/x-cmu-raster',
        'rdf'     => 'application/rdf+xml',
        'rgb'     => 'image/x-rgb',
        'rm'      => 'application/vnd.rn-realmedia',
        'roff'    => 'application/x-troff',
        'rss'     => 'application/rss+xml',
        'rtf'     => 'text/rtf',
        'rtx'     => 'text/richtext',
        'sgm'     => 'text/sgml',
        'sgml'    => 'text/sgml',
        'sh'      => 'application/x-sh',
        'shar'    => 'application/x-shar',
        'silo'    => 'model/mesh',
        'sit'     => 'application/x-stuffit',
        'skd'     => 'application/x-koan',
        'skm'     => 'application/x-koan',
        'skp'     => 'application/x-koan',
        'skt'     => 'application/x-koan',
        'smi'     => 'application/smil',
        'smil'    => 'application/smil',
        'snd'     => 'audio/basic',
        'so'      => 'application/octet-stream',
        'spl'     => 'application/x-futuresplash',
        'src'     => 'application/x-wais-source',
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc'  => 'application/x-sv4crc',
        'svg'     => 'image/svg+xml',
        'svgz'    => 'image/svg+xml',
        'swf'     => 'application/x-shockwave-flash',
        't'       => 'application/x-troff',
        'tar'     => 'application/x-tar',
        'tcl'     => 'application/x-tcl',
        'tex'     => 'application/x-tex',
        'texi'    => 'application/x-texinfo',
        'texinfo' => 'application/x-texinfo',
        'tif'     => 'image/tiff',
        'tiff'    => 'image/tiff',
        'tr'      => 'application/x-troff',
        'tsv'     => 'text/tab-separated-values',
        'txt'     => 'text/plain',
        'ustar'   => 'application/x-ustar',
        'vcd'     => 'application/x-cdlink',
        'vrml'    => 'model/vrml',
        'vxml'    => 'application/voicexml+xml',
        'wav'     => 'audio/x-wav',
        'wbmp'    => 'image/vnd.wap.wbmp',
        'wbxml'   => 'application/vnd.wap.wbxml',
        'wml'     => 'text/vnd.wap.wml',
        'wmlc'    => 'application/vnd.wap.wmlc',
        'wmls'    => 'text/vnd.wap.wmlscript',
        'wmlsc'   => 'application/vnd.wap.wmlscriptc',
        'wrl'     => 'model/vrml',
        'xbm'     => 'image/x-xbitmap',
        'xht'     => 'application/xhtml+xml',
        'xhtml'   => 'application/xhtml+xml',
        'xls'     => 'application/vnd.ms-excel',
        'xml'     => 'application/xml',
        'xpm'     => 'image/x-xpixmap',
        'xsl'     => 'application/xml',
        'xslt'    => 'application/xslt+xml',
        'xul'     => 'application/vnd.mozilla.xul+xml',
        'xwd'     => 'image/x-xwindowdump',
        'xyz'     => 'chemical/x-xyz',
        'zip'     => 'application/zip'
    );
    return $types[strtolower($ext)];
}

function generateQRHash($id, $prefix, $key)
{
    $hash = hash_hmac('md5', $id, $key);
    return $prefix.$hash;
}

function generateQrImage($qrContent = '', $width = 200, $height = 200,$imageType = 'png'){
    if($imageType == 'png'){
        $renderer = new \BaconQrCode\Renderer\Image\Png();
    }
    $renderer->setWidth($width);
    $renderer->setHeight($height);
    $writer = new \BaconQrCode\Writer($renderer);
    return $writer->writeString($qrContent);
}

function getDateFormat($format_id)
{
    $format = "%Y/%m/%d";
    if($format_id == 2)
        $format = "%d. %B %Y";
    elseif($format_id == 3)
        $format = "%d. %B %Y";
    elseif($format_id == 4)
        $format = "%d. %B %Y";
    elseif($format_id == 5)
        $format = "%Y %B %d d.";
    elseif($format_id == 6)
        $format = "%d. %B %Y";
    elseif($format_id == 7)
        $format = "%d %B %Y";
    elseif($format_id == 8)
        $format = "%d.%m.%Y";

    return $format;
}

function object_to_array($data)
{
    if (is_array($data) || is_object($data)) {
        $result = array();
        foreach ($data as $key => $value) {
            $result[$key] = object_to_array($value);
        }
        return $result;
    }
    return $data;
}

function date_range($first, $last, $output_format = 'd/m/Y', $step = '+1 day')
{
    $dates = array();
    $current = strtotime($first);
    $last = strtotime($last);
    while ($current <= $last) {
        $dates[] = date($output_format, $current);
        $current = strtotime($step, $current);
    }
    return $dates;
}

function validateEmail($email) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    }

    return false;
}

function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

function between ($from, $to, $inthat)
{
    return before ($to, after($from, $inthat));
}

function after ($from, $inthat)
{
    if (!is_bool(strpos($inthat, $from)))
        return substr($inthat, strpos($inthat,$from)+strlen($from));
}

function before ($from, $inthat)
{
    return substr($inthat, 0, strpos($inthat, $from));
}

function has_dupes($array) {
    $dupe_array = array();
    foreach ($array as $val) {
        if (++$dupe_array[$val] > 1) {
            return true;
        }
    }
    return false;
}

/**
 * getTimeSlots
 *
 * @param  mixed $duration
 * @param  mixed $break
 * @param  mixed $stTime
 * @param  mixed $enTime
 * @return void
 */
function getTimeSlots($duration, $break, $stTime, $enTime)
{
    $start = new DateTime($stTime);
    $end = new DateTime($enTime);
    $interval = new DateInterval("PT" . $duration . "M");
    $breakInterval = new DateInterval("PT" . $break . "M");
    for ($intStart = $start;
        $intStart < $end;
        $intStart->add($interval)->add($breakInterval)) {
        $endPeriod = clone $intStart;
        $endPeriod->add($interval);
        if ($endPeriod > $end) {
        $endPeriod = $end;
        }
        $periods[] = $intStart->format('H:i') .
        ' - ' .
        $endPeriod->format('H:i');
    }
    return $periods;
}

function getLogData($event_id = null){

    $event_id = $event_id!=null?$event_id:request()->get('event_id');
    $data=[
        'app_type'=>'event_center',
        'logged_by_user_type' => 'organizer',
        'event_id' => $event_id,
    ];
    $event = \App\Models\Event::find($event_id);

    if(Request::is('api/organizer*')){
        $data['app_type']='organizer_app';
    }elseif(Request::is('registration/event*')){
        $data['app_type']='new_registration_site';
        $data['logged_by_user_type'] = 'attendee';
        if($event) {
            $data['organizer_id'] = $event->organizer_id;
        }
    }elseif(Request::is('api/wizard*') || $event->type==1){
        $data['app_type']='PNP';
    }
    if(auth()->guard('organizer') && !Request::is('registration/event*')){
        $data['logged_by_user_type'] = 'organizer';
        $data['logged_by_id'] =  auth()->guard('organizer')->user()->id;
        $data['organizer_id'] = auth()->guard('organizer')->user()->id;
        if(auth()->guard('organizer')->user()->parent_id>0){
            $data['organizer_id'] =  auth()->guard('organizer')->user()->parent_id;
        }

    }elseif(auth()->guard('web')){
        $data['logged_by_user_type'] = 'attendee';
        $data['logged_by_id'] =  auth()->guard('web')->user()->id;
        $data['organizer_id'] = auth()->guard('web')->user()->id;
        if(auth()->guard('organizer')->user()->parent_id>0){
            $data['organizer_id'] =  auth()->guard('web')->user()->parent_id;
        }

    }elseif(auth()->guard('attendee-web') || auth()->guard('attendee')){
        $data['logged_by_user_type'] = 'attendee';
        $data['logged_by_id'] = auth()->guard('attendee')->user()->id;
        $data['organizer_id'] = auth()->guard('attendee')->user()->organizer_id;

    }elseif(auth()->guard('lead-user')){
        $data['logged_by_user_type'] = 'leadUser';
        $data['logged_by_id'] = auth()->guard('lead-user')->user()->id;
        if($event)
        $data['organizer_id'] = $event->organizer_id;
    }

    if(!isset($data['organizer_id']) || empty($data['organizer_id'])){
        if($event){
            $data['organizer_id'] = $event->organizer_id;
        }else{
            $data['organizer_id'] = -20;
        }
    }
    return $data;
}

function getIPAddress() {
    if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function sanitizeLabel($label)
{
    $label = str_replace(' ', '_', $label);

    $label = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $label);
    
    return $label;

}
