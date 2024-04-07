<?php


    namespace App\Helpers\Agora;


    use App\Models\AgoraCallAnalytics;
    use App\Models\AgoraCallDetail;
    use App\Models\AgoraChannel;
    use App\Models\EventMeetingHistory;
    use Carbon\Carbon;
    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\RequestException;
    use Illuminate\Support\Facades\Log;

    class AnalyticsAPI
    {
        private $client;

        // Sync day interval should be less than 14. because agora API don't work when fetching data older than 14 days.
        const SYNC_DAY_INTERVAL = 10;
        public function __construct()
        {
            $this->client = new Client(['cookies' => true, 'verify' => false]);
        }

        public function doLogin()
        {
            try {
                $response = $this->client->request('post', env('AGORA_CONSOLE_LOGIN_URL'), [
                    'headers' => [
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:86.0) Gecko/20100101 Firefox/86.0'
                    ],
                    'json' => [
                        'email' => env('AGORA_CONSOLE_USERNAME'),
                        'password' => env('AGORA_CONSOLE_PASSWORD')
                    ]
                ]);
            }catch (RequestException $e){
                $status = $e->getResponse()->getStatusCode();
                $body = $e->getResponse()->getBody()->getContents();
                $message =  $e->getMessage();
                Log::error("Agora Console Login Error: message: $message, status: $status, body : $body");
                return false;
            }

            return true;
        }

        public function syncCallAnalytics()
        {
            if (!$this->doLogin()) {
                return false;
            }

            $meeting_histories = AgoraChannel::where('updated_at', '>=', Carbon::now()->subDays(self::SYNC_DAY_INTERVAL)->startOfDay()->toDateTimeString())->groupBy('channel')->get();
            foreach ($meeting_histories as $meeting) {
                $data = $this->fetchAnalyticsData($meeting['channel']);
                if($data !== false && isset($data['callSessions'])){
                    foreach ($data['callSessions'] as $item) {
                        $agora_model = $this->saveCallAnalytics($meeting, $item);
                        $this->fetchCallDetail($agora_model);
                    }
                }
            }
            return true;
        }

        public function fetchAnalyticsData($cname)
        {
            $total_records = 150;
            $page_size = 150;
            $skip = 0;

            try {
                $response = $this->client->request('get', 'https://analytics-lab.agora.io/api/analytics/callSearch', [
                    'query' => [
                        'fromTs' => Carbon::now()->subDays(self::SYNC_DAY_INTERVAL)->startOfDay()->getTimestamp(),
                        'toTs' => Carbon::now()->getTimestamp(),
                        'from' => $skip,
                        'size' => $page_size,
                        'projectId' => env('AGORA_CONSOLE_PROJECT_ID'),
                        'cname' => $cname,
//                        'uids' => $uid
                    ]
                ]);
            } catch (RequestException $e) {
                $status = $e->getResponse()->getStatusCode();
                $body = $e->getResponse()->getBody()->getContents();
                $message = $e->getMessage();
                Log::error("Agora Console Analytics API: message: $message, status: $status, body : $body");
                return false;
            }
            catch (\Exception $e){
                $message = $e->getMessage();
                Log::error("Agora Console Analytics API: message: $message");
                dump("Agora Console Analytics API: message: $message");
                return false;
            }

            $data = $response->getBody()->getContents();
            $data = json_decode($data, true);

            return $data;
        }

        public function saveCallAnalytics($meeting, $item)
        {
            return AgoraCallAnalytics::updateOrCreate(
                ['agora_id' => $item['id']],
                [
                    'event_id'        => $meeting['event_id'],
//                    'attendee_id'     => $meeting['attendee_id'],
                    'vid'             => $item['vid'],
                    'project_id'      => env('AGORA_CONSOLE_PROJECT_ID'),
                    'created_ts'      => $item['createdTs'],
                    'destroyed_ts'    => $item['destroyedTs'],
                    'cname'           => $item['cname'],
                    'cid'             => $item['cid'],
                    'finished'        => $item['finished'],
                    'ts'              => $item['ts'],
                    'mode'            => $item['mode'],
                    'duration'        => $item['duration'],
                    'permanented'     => $item['permanented'],
                    'created_ts_at'   => date('Y-m-d H:i:s', $item['createdTs']),
                    'destroyed_ts_at' => date('Y-m-d H:i:s', $item['destroyedTs']),
                    'ts_at'           => date('Y-m-d H:i:s', $item['ts']),
                ]
            );
        }

        private function fetchCallDetail(AgoraCallAnalytics $model)
        {
            try {
                $response = $this->client->request('GET', 'https://analytics-lab.agora.io/api/analytics/mergedUserlist', [
                    'query' => [
                        'id'       => $model->agora_id,
                        'fromTs'   => $model->created_ts,
                        'toTs'     => $model->destroyed_ts ? $model->destroyed_ts : time(),
                        'longTerm' => false,
                    ]
                ]);
            } catch (\Exception $e){
                Log::error("Unable to fetch detail for ". $model->agora_id);
                return ;
            }

            $data = $response->getBody()->getContents();
            $data = json_decode($data, true);
            // sync details in db
            foreach ($data['userdata'] as $detail) {
                if($model->cname == "Eventbuizz-485")
                    dump($detail['duration']);
                AgoraCallDetail::updateOrCreate(
                    [
                        'call_id'     => $model->id,
                        'attendee_id' => $detail['uid'],
                    ],
                    [
                        'sdk_version' => $detail['sdkVersion'],
                        'quit_state'  => $detail['quitState'],
                        'loc'         => $detail['loc'],
                        'account'     => $detail['account'],
                        'join_ts'     => $detail['joinTs'],
                        'leave_ts'    => $detail['leaveTs'],
                        'duration'    => $detail['duration'],
                        'ip'          => $detail['ip']
                    ]
                );
            }
        }

    }