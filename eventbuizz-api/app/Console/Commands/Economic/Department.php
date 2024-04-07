<?php

namespace App\Console\Commands\Economic;

use Illuminate\Console\Command;

use App\Models\EconomicDepartment;

class Department extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:departments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch departments';

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
        //latest department
        $departmentNumber = (int) EconomicDepartment::orderBy('departmentNumber', 'DESC')->value('departmentNumber');
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'https://restapi.e-conomic.com/departments?filter=departmentNumber$gt:' . $departmentNumber . '&pagesize=100', [
            'headers' => [
                'X-AppSecretToken' => config("services.economic.X-AppSecretToken"),
                'X-AgreementGrantToken'     => config("services.economic.X-AgreementGrantToken"),
                'Content-Type'      => 'application/json'
            ]
        ]);
        $content = $response->getBody()->getContents();
        $content = json_decode($content, true);
        if (count($content['collection'] ?? []) > 0) {
            foreach ($content['collection'] as $row) {
                EconomicDepartment::create([
                    "departmentNumber" => $row["departmentNumber"],
                    "name" => $row["name"]
                ]);
            }
        }

        $this->info('departments executed successfully!');
    }
}
