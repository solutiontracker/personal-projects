<?php


    namespace App\Helpers\DynamicsCRM;


    use App\Models\DynamicsToken;
    use App\Models\SalesforceApiLog;
    use GuzzleHttp\Client;
    use GuzzleHttp\Exception\RequestException;

    class DynamicsWebAPIClient
    {
        public $client;
        public $token;
        public $api_url;
        public $headers = [];

        /**
         * @param DynamicsToken $token
         */
        public function __construct(DynamicsToken $token)
        {
            $this->token = $token;
            $this->api_url = $this->token->org_url . '/api/data/v' . env('DYNAMICS_API_VERSION') . '';
            $this->client = new Client();
            $this->headers = [
                'Authorization' => 'Bearer ' . $this->token->access_token,
                'Accept' => 'application/json',
                'Conte-type' => 'application/json',
                'Prefer' => 'return=representation',
                'OData-MaxVersion' => '4.0',
                'OData-Version' => '4.0',
            ];
        }

        /**
         * @param String $entity_name
         * @param String $key
         * @return false|mixed
         */
        public function get(string $entity_name, string $key)
        {
            $url = "{$this->api_url}/$entity_name($key)";

            try {
                $response = $this->client->request('GET', $url, [
                    'headers' => $this->headers
                ]);
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    $contents = $e->getResponse()->getBody()->getContents();
                    $status = $e->getResponse()->getStatusCode();
                    $this->logRequest('GET', $entity_name, $contents, $url, $status);
                }
                return false;
            }

            $contents = $response->getBody()->getContents();
            $status = $response->getStatusCode();

            $this->logRequest('GET', $entity_name, $contents, $url, $status);

            return json_decode($contents);
        }

        /**
         * @param String $entity_name
         * @param array $filter
         * @return false|mixed
         */
        public function queryAll(string $entity_name, $filter = [])
        {

            $filter_str = (is_array($filter) && count($filter) > 0) ? $this->getFilterString($filter) : '';

            $url = "{$this->api_url}/$entity_name?$filter_str";

            try {
                $response = $this->client->request('GET', $url, [
                    'headers' => $this->headers
                ]);
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    $contents = $e->getResponse()->getBody()->getContents();
                    $status = $e->getResponse()->getStatusCode();
                    $this->logRequest('GET', $entity_name, $contents, $url, $status);
                }
                return false;
            }
            $contents = $response->getBody()->getContents();
            $status = $response->getStatusCode();

            $this->logRequest('GET', $entity_name, $contents, $url, $status);
            return json_decode($contents);
        }

        /**
         * @param String $entity_name
         * @param array $data
         * @return false|mixed
         * @throws \GuzzleHttp\Exception\GuzzleException
         */
        public function create(string $entity_name, array $data)
        {
            $url = "{$this->api_url}/$entity_name";
            try {
                $response = $this->client->request('POST', $url, [
                    'headers' => $this->headers,
                    'json' => $data
                ]);
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    $contents = $e->getResponse()->getBody()->getContents();
                    $status = $e->getResponse()->getStatusCode();
                    $this->logRequest('POST', $entity_name, $contents, json_encode($data), $status);
                }
                return false;
            }
            $contents = $response->getBody()->getContents();
            $status = $response->getStatusCode();

            $this->logRequest(' POST', $entity_name, $contents, $url, $status);
            dump($entity_name. " created ");
            return json_decode($contents);
        }

        /**
         * @param String $entity_name
         * @param String $key
         * @param array $data
         * @return false|mixed
         * @throws \GuzzleHttp\Exception\GuzzleException
         */
        public function update(string $entity_name, string $key, array $data)
        {
            $url = "{$this->api_url}/$entity_name($key)";
            try {
                $response = $this->client->request('PATCH', $url, [
                    'headers' => $this->headers,
                    'json' => $data
                ]);
            } catch (RequestException $e) {
                if ($e->hasResponse()) {
                    $contents = $e->getResponse()->getBody()->getContents();
                    $status = $e->getResponse()->getStatusCode();
                    $this->logRequest('PATCH', $entity_name, $contents, json_encode($data), $status);
                }
                return false;
            }
            $contents = $response->getBody()->getContents();
            $status = $response->getStatusCode();
            $this->logRequest('PATCH', $entity_name, $contents, $url, $status);
            dump($entity_name. " updated ");
            return json_decode($contents);
        }

        /**
         * @param $filter
         * @return string
         */
        private function getFilterString($filter)
        {
            $filter_str = '$filter=';
            $and = '';
            foreach ($filter as $key => $value) {

                $filter_str .= "$key eq '$value' $and ";
                $and = 'and';
            }

            return trim($filter_str);
        }

        /**
         * @param $action
         * @param $entity
         * @param $response
         * @param string $input
         */

        public function logRequest($action, $entity, $response, $input = '', $status)
        {
            $salesforceLog = new SalesforceApiLog();
            $salesforceLog->organizer_id = $this->token->organizer_id;
            $salesforceLog->alias = 'dynamics';
            $salesforceLog->action = $action;
            $salesforceLog->object = $entity;
            $salesforceLog->input = $input;
            $salesforceLog->response = $response;
            $salesforceLog->status_code = $status;

            $salesforceLog->save();
        }


    }