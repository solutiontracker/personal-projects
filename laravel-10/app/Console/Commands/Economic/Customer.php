<?php

namespace App\Console\Commands\Economic;

use Illuminate\Console\Command;

use App\Models\EconomicCustomer;

class Customer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch customers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //latest customer
        $customer_number = (int) EconomicCustomer::orderBy('customerNumber', 'DESC')->value('customerNumber');
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://restapi.e-conomic.com/customers?filter=customerNumber$gt:' . $customer_number . '&pagesize=100', [
            'headers' => [
                'X-AppSecretToken' => config("services.economic.X-AppSecretToken"),
                'X-AgreementGrantToken'     => config("services.economic.X-AgreementGrantToken"),
                'Content-Type'      => 'application/json'
            ]
        ]);
        $content = $response->getBody()->getContents();
        $content = json_decode($content, true);
        if (count($content['collection']  ?? []) > 0) {
            foreach ($content['collection'] as $row) {
                EconomicCustomer::create([
                    "customerNumber" => $row["customerNumber"],
                    "email" => $row["email"],
                    "name" => $row["name"],
                    "currency" => $row["currency"],
                    "paymentTermsNumber" => $row["paymentTerms"]["paymentTermsNumber"],
                    "customerGroupNumber" => $row["customerGroup"]["customerGroupNumber"],
                    "balance" => $row["balance"],
                    "address" => $row["address"],
                    "dueAmount" => $row["dueAmount"],
                    "corporateIdentificationNumber" => (isset($row["corporateIdentificationNumber"]) ? $row["corporateIdentificationNumber"] : 0),
                    "city" => $row["city"],
                    "country" => $row["country"],
                    "ean" => $row["ean"],
                    "zip" => $row["zip"],
                    "website" => $row["website"],
                    "vatZoneNumber" => $row["vatZone"]["vatZoneNumber"],
                    "layoutNumber" => $row["layout"]["layoutNumber"],
                    "customerContactNumber" => $row["customerContact"]["customerContactNumber"],
                    "lastUpdated" => ($row["lastUpdated"] ? \Carbon\Carbon::parse($row["lastUpdated"])->toDateTimeString() : NULL)
                ]);
            }
        }

        $this->info('customers executed successfully!');
    }
}
