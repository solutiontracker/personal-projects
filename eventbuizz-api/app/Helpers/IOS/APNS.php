<?php


    namespace App\Helpers\IOS;


    use App\Models\CronPushNotification;
    use App\Models\OrganizerAPNS;
    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\RequestException;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Str;

    class APNS
    {

        public function sendAllNotification()
        {

            $notifications = CronPushNotification::has('alert')->where('status', '0')->where('deviceType', 'ios')->offset(0)->limit(500)->get();

            CronPushNotification::has('alert')->where('status', '0')->where('deviceType', 'ios')->update(['status' => '2']);

            foreach ($notifications as $item) {

                $payload = json_encode([
                    'aps' => [
                        'badge' => $item->badge_count,
                        "alert" => [
                            "title" => $item->alertTtile,
                            "body"  => $item->alertDescription,
                        ]
                    ],
                    'custom data' => [
                        'text'       => $item->alertTtile,
                        'type'       => 'news',
                        'detail'     => $item->alertDescription,
                        'id'         => $item->alert_id,
                        'alert_date' => $item->alert_date,
                        'alert_time' => $item->alert_time
                    ]
                ]);

                try {
                    $response = $this->send($item->organizer_id, $item->deviceToken, $payload);
                } catch (\Exception $e) { }

                $item->update([
                    'status'   => '1',
                    'responce' => $response
                ]);
            }

        }

        /**
         * @param int $organizer_id
         * @param string $device_token
         * @param string $payload
         * @return false|string
         * @throws \GuzzleHttp\Exception\GuzzleException
         */
        public function send(int $organizer_id, string $device_token, string $payload): string
        {
            //Create new uuid as unique notificaiton ID.
            $apns_id = (string)Str::uuid();
            $apns    = OrganizerAPNS::where('organizer_id', $organizer_id)->first();

            if (!$apns) {
                $apns    = OrganizerAPNS::where('is_default', 1)->first();
                if(!$apns)
                {
                    return json_encode([
                        'message' => 'organizer certificate not found',
                        'error'   => true
                    ]);
                }
            }

            $payload = json_decode($payload, true);

            if(isset($payload['aps']['badge'])) {
                $payload['aps']['badge'] = (int) $payload['aps']['badge'];
            }

            $payload = json_encode($payload);
            
            $client = new Client();

            try {
                $response = $client->request('POST', $this->getNotificationUrl($device_token), [
                    'timeout' => 30,
                    'version' => 2.0,
                    'headers' => [
                        'Content-Type'    => 'application/json',
                        'apns-expiration' => '0',
                        'apns-topic'      => $apns['apns_topic'],
                        'authorization'   => 'bearer ' . $apns['jwt_token'],
                        'apns-push-type'  => 'alert',
                        'apns-id'         => $apns_id
                    ],
                    'body'    => $payload
                ]);
            } catch (\Exception $e) {
                return json_encode([
                    'status_code' => $e->getResponse()->getStatusCode(),
                    'body'        => $e->getResponse()->getBody()->getContents(),
                    'message'     => "Server error",
                    'error'       => true
                ]);
            }

            return json_encode([
                'status_code' => $response->getStatusCode(),
                'apns_id'     => $apns_id,
                'response'    => $response->getBody()->getContents(),
                'err'         => false,
            ]);

        }

        /**
         * @param string $device_token
         * @return string
         */
        public function getNotificationUrl(string $device_token): string
        {
            $url = (App::environment('production')) ? 'https://api.push.apple.com:443' : 'https://api.sandbox.push.apple.com:443';
            $url = $url . $path = '/3/device/' . $device_token;
            return $url;
        }
    }