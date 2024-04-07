<?php

    namespace App\Helpers\DynamicsCRM;


    use App\Models\DynamicsToken;

    class DynamicsAccountHelper
    {
        const ENTITY_TYPE = 'accounts';
        const COLUMN_SET = ['accountid', 'name'];
        private $client = null;
        private $rules = null;
        private $token;

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
        public function getWebAPIClient()
        {
            return (new DynamicsWebAPIClient($this->token));
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
        public function searchByName($name)
        {
            $name = trim($name);

            //Fetch all account list matching the givent $name. But returns the Id of first.
            $result = $this->client->queryAll(self::ENTITY_TYPE, ['name' => $name]);
            if ($result !== false) {
                if (count($result->value) > 0) {
                    return $result->value[0]->accountid;
                }
            }
            return false;
        }

        /**
         * @param $attendee
         * @return false
         */
        public function create($name)
        {
            $name = trim($name);
            $result = $this->client->create(self::ENTITY_TYPE, ['name' => $name]);

            if ($result !== false)
                return $result->accountid;
            else
                return false;
        }

        /**
         * @param $id
         * @param $attendee
         * @return false|mixed
         */
        public function update($id, $name)
        {
            $data = ["name" => trim($name)];
            $result = $this->client->update(self::ENTITY_TYPE, $id, $data);

            return $result;
        }

        public function findOrCreate($name)
        {

            $account = $this->searchByName($name);

            if ($account !== false) {
                return $account;
            } else {
                return $this->create($name);
            }
        }
    }