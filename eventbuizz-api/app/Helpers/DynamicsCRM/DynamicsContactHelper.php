<?php


namespace App\Helpers\DynamicsCRM;


use App\Models\DynamicsToken;

class DynamicsContactHelper
{
    const ENTITY_TYPE = 'contacts';
    const COLUMN_SET = ['contactid', 'fullname', 'birthdate', 'department', 'description', 'emailaddress1', 'firstname', 'lastname', '_accountid_value', 'mobilephone'];
    private $client = null;
    private $token;
    private $rules;

    /**
     * DynamicsContactHelper constructor.
     * @param DynamicsToken $token
     */
    public function __construct(DynamicsToken $token, $rules)
    {
        $this->token = $token;
        $this->rules = $rules;
        $this->client = $this->getWebAPIClient();
    }

    /**
     * @return DynamicsWebAPIClient
     */
    public function getWebAPIClient(){

        return new DynamicsWebAPIClient($this->token);
    }

    /**
     * @param $id
     * @return false|mixed
     */
    public function get($id)
    {
        $result = $this->client->get(self::ENTITY_TYPE, $id);

        return $result;
    }

    /**
     * @param $email
     * @return false
     */
    public function searchByEmail($email){
        $email = trim($email);

        //Fetch all account list matching the givent $name. But returns the Id of first.
        $result = $this->client->queryAll( self::ENTITY_TYPE, ['emailaddress1'=> $email]);

        if($result !== false) {
            if (count($result->value) > 0) {
                return $result->value[0]->contactid;
            }
        }
        return false;
    }

    /**
     * @param $attendee
     * @return false
     */
    public function create($attendee)
    {
        $data = $this->mapAttendee($attendee);
        $result = $this->client->create( self::ENTITY_TYPE, $data );

        if($result !== false)
            return $result->contactid;
        else
            return false;
    }

    /**
     * @param $id
     * @param $attendee
     * @return false|mixed
     */
    public function update($id, $attendee)
    {
        $contact = $this->mapAttendee($attendee);

        $result = $this->client->update(self::ENTITY_TYPE, $id, $contact);

        return $result;
    }

    /**
     * @param $attendee
     * @return false|mixed
     */
    public function upsert($attendee){
        $id = $this->searchByEmail($attendee['email']);
        if($id === false){
            return  $this->create($attendee);
        }
        else{
            return $this->update($id, $attendee);
        }
    }

    /**
     * @param $attendee
     * @param false $upsert
     * @return array
     */
    public function mapAttendee($attendee, $upsert = false)
    {
        $record = [];
        if(!empty($attendee['first_name'])) {
            $record['firstname'] = $attendee['first_name'];
        }
        if(!empty($attendee['last_name'])){
            $record['lastname'] = $attendee['last_name'];
        }else{
            $record['lastname'] = 'dummy';
        }

        if(!empty($attendee['email'])) {
            $record['emailaddress1'] = $attendee['email'] ?? null;
        }

        if(!empty($attendee['phone'])) {
            $record['mobilephone'] = $attendee['phone'] ?? null;
        }

        if(isset($attendee['gender']) && $attendee['gender'] !== 'n/a') {
            $record['gendercode'] = $attendee['gender'] == 'male' ? 1 : 2;
        }

        if (isset($attendee['BIRTHDAY_YEAR']) && $attendee['BIRTHDAY_YEAR'] !== "0000-00-00 00:00:00") {
            $record['birthdate'] = date("Y-m-d", strtotime($attendee['BIRTHDAY_YEAR']));
        }

        if(isset($attendee['company_name']) && !empty($attendee['company_name'])) {
            if(isset($attendee['accountid'])) {
                $record['parentcustomerid_account@odata.bind'] = '/accounts(' . $attendee['accountid'] . ')';
            }else{
                $record['parentcustomerid_account@odata.bind'] = '/accounts(' . $this->getAccountId($attendee) . ')';
            }
        }

        if(isset($attendee['about'])) {
            $record['description'] = $attendee['about'] ?? null;
        }

        if(isset($this->rules['use_company_address']) && $this->rules['use_company_address']) {
            dump("adding company addres: ". $this->rules['use_company_address']);
            if (isset($attendee['billing']['billing_company_street']) || isset($attendee['billing']['billing_company_house_number'])) {
                $record['address1_line1'] = $attendee['billing']['billing_company_street']  .' '.  $attendee['billing']['billing_company_house_number'] ?? null;
            }

            if (isset($attendee['billing']['billing_company_post_code'])) {
                $record['address1_postalcode'] =$attendee['billing']['billing_company_post_code'] ?? null;
            }

            if (isset($attendee['billing']['billing_company_city'])) {
                $record['address1_city'] = $attendee['billing']['billing_company_city'] ?? null;
            }

            if (isset($attendee['billing']['country']['name'] )) {
                $record['address1_country'] = $attendee['billing']['country']['name'] ?? null;
            }

        }else{
            dump("adding private addres: ". $this->rules['use_company_address']);
            if (isset($attendee['private_street'])) {
                $record['address1_line1'] = $attendee['private_street'] . ' ' . $attendee['private_house_number'] ?? null;
            }
            if (isset($attendee['private_post_code'])) {
                $record['address1_postalcode'] = $attendee['private_post_code'] ?? null;
            }

            if (isset($attendee['private_city'])) {
                $record['address1_city'] = $attendee['private_city'] ?? null;
            }

            if (isset($attendee['private_country'])) {
                $record['address1_country'] = $attendee['private_country'] ?? null;
            }
        }
        //Organization info
        if(isset($attendee['title'])) {
            $record['jobtitle'] = $attendee['title'] ?? null;
        }
        if(isset($attendee['department'])) {
            $record['department'] = $attendee['department'] ?? null;
        }

        return $record;
    }

    public function getAccountId($attendee){
        if(isset($attendee['company_name']) && !empty($attendee['company_name'])){
            $accountHelper = new DynamicsAccountHelper($this->token);
            return $accountHelper->findOrCreate($attendee['company_name']);
        }
    }
}