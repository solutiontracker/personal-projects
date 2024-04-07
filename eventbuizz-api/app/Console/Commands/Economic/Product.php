<?php

namespace App\Console\Commands\Economic;

use Illuminate\Console\Command;

use App\Models\EconomicProduct;

class Product extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch products';

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
        $count = (int) EconomicProduct::orderBy('id', 'DESC')->count();
        $pages = (int)($count / 1000);
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://restapi.e-conomic.com/products?skipPages=' . $pages . '&pagesize=1000', [
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
                $exist = EconomicProduct::where('productNumber', $row["productNumber"])->first();
                if (!$exist) {
                    EconomicProduct::create([
                        "productNumber" => $row["productNumber"],
                        "name" => $row["name"],
                        "description" => $row["description"],
                        "recommendedPrice" => $row["salesPrice"],
                        "salesPrice" => $row["salesPrice"],
                        "lastUpdated" => ($row["lastUpdated"] ? \Carbon\Carbon::parse($row["lastUpdated"])->toDateTimeString() : NULL),
                        "productGroupNumber" => $row["productGroup"]['productGroupNumber'],
                        "barred" => $row["barred"],
                    ]);
                }
            }
        }

        $this->info('products executed successfully!');
    }
}
