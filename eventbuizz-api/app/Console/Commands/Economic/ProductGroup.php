<?php

namespace App\Console\Commands\Economic;

use Illuminate\Console\Command;

use App\Models\EconomicProductGroup;

class ProductGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:product_groups';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch product groups';

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
        //latest product group
        $group_number = (int) EconomicProductGroup::orderBy('productGroupNumber', 'DESC')->value('productGroupNumber');
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://restapi.e-conomic.com/product-groups?filter=productGroupNumber$gt:' . $group_number . '&pagesize=1000', [
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
                EconomicProductGroup::create([
                    "productGroupNumber" => $row["productGroupNumber"],
                    "name" => $row["name"],
                ]);
            }
        }

        $this->info('product group executed successfully!');
    }
}
