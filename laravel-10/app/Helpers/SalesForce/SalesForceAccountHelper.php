<?php


    namespace App\Helpers\Salesforce;

    use App\Models\SalesforceApiLog;
    use Frankkessler\Salesforce\Facades\Salesforce;
    use Frankkessler\Salesforce\SalesforceConfig;
    use Omniphx\Forrest\Exceptions\SalesforceException;

    class SalesForceAccountHelper
    {
        const OBJECT_TYPE = 'Account';

        private $rules = [];

        public function __construct($rules)
        {
            $this->rules = $rules;

        }

        /**
         * @param $id
         * @return false | array
         *
         */
        public function get($id)
        {
            try {
                $result = \EBForrest::sobjects(self::OBJECT_TYPE.'/' . $id, ['method' => 'GET']);

            }
            catch (SalesforceException $e){
                $this->logRequest('get', $e->getResponse()->getBody()->getContents(), $id);
                return false;
            }

            $this->logRequest('get', $result, $id);
            return $result;
        }

        /**
         * @param $name
         * @return array
         */
        public function searchByName($name)
        {
            $name     = trim($name);
            $accounts = [];
            $soql   = "SELECT Id  FROM ".self::OBJECT_TYPE." WHERE Name = '$name' LIMIT 1";
            $result = \EBForrest::query($soql);

            $this->logRequest('search', $result, $name);
            dump($result);
            if ($result['totalSize'] > 0) {
                $accounts = array_column($result['records'], 'Id');
            }

            return $accounts;
        }

        public function create($name)
        {

            try {
                $result = \EBForrest::sobjects(self::OBJECT_TYPE,
                    [
                        'method' => 'POST',
                        'body'   => [
                            'Name' => trim($name)
                        ]
                    ]);

                $this->logRequest('create', $result, $name);
            } catch (SalesforceException $e) {
                $this->logRequest('create', $e->getResponse()->getBody()->getContents(), $name);
                return false;
            }

            if (isset($result['id'])) return $result['id'];
            return false;
        }

        public function update($id, $name)
        {
            try {
                $result = \EBForrest::sobjects(self::OBJECT_TYPE.'/'.$id,
                    [
                        'method' => 'PATCH',
                        'body'   => [
                            'Name' => trim($name)
                        ]
                    ]);

                $this->logRequest('update', [], $name);
            } catch (SalesforceException $e) {
                $this->logRequest('update', $e->getResponse()->getBody()->getContents(), $name);
                return false;
            }

           return true;
        }

        public function delete($id)
        {
            try {
                $result = \EBForrest::sobjects(self::OBJECT_TYPE.'/' . $id, ['method' => 'DELETE']);
            }
            catch (SalesforceException $e){
                $this->logRequest('DELETE', $e->getResponse()->getBody()->getContents(), $id);
                return false;
            }

            return true;
        }

        public function findOrCreate($name){

            $accounts = $this->searchByName($name);
            dump($accounts);
            if(count($accounts) > 0){
                return $accounts[0];
            }else{
                return $this->create($name);
            }
        }

        public function logRequest($action, $result, $input = '')
        {
            $salesforceLog = new SalesforceApiLog();
            $salesforceLog->organizer_id = 1;
            $salesforceLog->action = $action;
            $salesforceLog->object = self::OBJECT_TYPE;
            $salesforceLog->input = $input;
            $salesforceLog->response = !is_string($result) ? json_encode($result) : $result;
            if(isset($result->success)){
                $salesforceLog->success = $result->success;
            }

            if(isset($result->http_status_code)){
                $salesforceLog->success = $result->http_status_code;
            }
            $salesforceLog->save();
        }
    }