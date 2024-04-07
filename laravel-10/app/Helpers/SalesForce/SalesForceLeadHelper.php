<?php


    namespace App\Helpers\SalesForce;

    use App\Models\SalesforceApiLog;
    use Omniphx\Forrest\Exceptions\SalesforceException;

    class SalesForceLeadHelper
    {
        const OBJECT_TYPE = 'Lead';
        private $rules = [];

        public function __construct($rules)
        {
            $this->rules = $rules;

        }


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

        public function searchByEmail($email)
        {
            $email   = trim($email);
            $contact = false;

            $soql   = "SELECT Id  FROM ".self::OBJECT_TYPE." WHERE Email = '$email' LIMIT 1";
            $result = \EBForrest::query($soql);

            $this->logRequest('search', $result, $email);

            if ($result['totalSize'] > 0) {
                $contact = array_column($result['records'], 'Id');
                return $contact[0];
            }

            return $contact;
        }

        public function create($attendee)
        {
            $objectData = $this->mapAttendee($attendee);

            try {
                $result = \EBForrest::sobjects(self::OBJECT_TYPE,
                    [
                        'method' => 'POST',
                        'body'   => $objectData
                    ]);

                $this->logRequest('create', $result, json_encode($objectData));
            } catch (SalesforceException $e) {
                $data = $e->getResponse()->getBody()->getContents();
                $data = json_decode($data, true);
                $this->logRequest('create', $data, json_encode($objectData));

                if(count($data) > 0 && $data[0]['errorCode'] == 'DUPLICATES_DETECTED') return 'DUPLICATES_DETECTED';

                return false;
            }

            if (isset($result['id'])) return $result['id'];

            return false;
        }

        public function update($id, $attendee)
        {
            $objectData = $this->mapAttendee($attendee);

            try {
                $result = \EBForrest::sobjects(self::OBJECT_TYPE.'/'.$id,
                    [
                        'method' => 'PATCH',
                        'body'   => $objectData
                    ]);

                $this->logRequest('update', $result, json_encode($objectData));
            } catch (SalesforceException $e) {
                $this->logRequest('update', $e->getResponse()->getBody()->getContents(), json_encode($objectData));
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


        public function mapAttendee($attendee, $upsert = false, $company_address = false)
        {
            $record = [];
            if ($upsert) {
                $record['ExternalId__c'] = 'EB' . $attendee['id'];
            }

            $record['FirstName'] = $attendee['first_name'] ?? null;

            if(!empty($attendee['last_name'])){
                $record['LastName'] = $attendee['last_name'];
            }else{
                $record['LastName'] =$attendee['first_name'];
            }

            $record['Email'] = $attendee['email'] ?? null;
            $record['Company'] = $attendee['company_name'] ?? null;
            $record['MobilePhone'] = $attendee['phone'] ?? null;
            $record['LeadSource'] = $attendee['event'];

            if(isset($this->rules['use_company_address']) && $this->rules['use_company_address']) {
                // company address fields.
                $record['Street'] = $attendee['billing']['billing_company_street'] .' '.  $attendee['billing']['billing_company_house_number']  ?? null;
                $record['PostalCode'] = $attendee['billing']['billing_company_post_code'] ?? null;
                $record['City'] = $attendee['billing']['billing_company_city'] ?? null;
                $record['Country'] = $attendee['billing']['country']['name'] ?? null;

            }else{
                // privte address fields.
                $record['Street'] = $attendee['private_street'] . ' ' . $attendee['private_house_number'] ?? null;
                $record['PostalCode'] = $attendee['private_post_code'] ?? null;
                $record['City'] = $attendee['private_city'] ?? null;
                $record['Country'] = $attendee['private_country'] ?? null;
            }
            //Organization info
            $record['Title'] = $attendee['title'] ?? null;
            return $record;
        }

//        public function bulkUpsert($attendees)
//        {
//            $operationType = 'upsert';
//            $objectType = self::OBJECT_TYPE;
//
//            $objectData = [];
//            foreach($attendees as $attendee){
//                $objectData[] = $this->mapAttendee($attendee, true);
//            }
//            $sale = new \Frankkessler\Salesforce\Salesforce();
//            $result = $sale->bulk()->runBatch($operationType, $objectType, $objectData, ['externalIdFieldName' => 'ExternalId__c']);
//            $this->logRequest('bulkupsert', $result);
//
//            if($result->id){
//                foreach ($result->batches as $batch) {
//                    echo $batch->numberRecordsProcessed;
//                    echo $batch->numberRecordsFailed;
//                    foreach ($batch->records as $record) {
//                        if(!$record['success']){
//                            echo 'Record Failed: '.json_encode($record);
//                        }
//                    }
//                }
//            }
//
//        }

        public function upsert($attendee){
            $id = $this->searchByEmail($attendee['email']);
            if($id === false){
                return  $this->create($attendee);
            }
            else{
                return $this->update($id, $attendee);
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