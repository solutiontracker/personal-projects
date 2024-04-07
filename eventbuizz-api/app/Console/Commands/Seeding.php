<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Seeding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:seed {table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Database seeding';

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
     * @return int
     */
    public function handle()
    {
        $seeds = array("conf_timezones","conf_countries","conf_labels","conf_labels_info","conf_templates_master","conf_fields","conf_fields_info","conf_modules","conf_add_ons","conf_languages","conf_date_formats","conf_eventbuizz_apps","conf_event_themes","conf_event_themes_module","conf_integrations","conf_organizer_apns","conf_organizer_permissions","conf_packages","conf_package_details","conf_themes","conf_themes_modules");

        $table = $this->argument('table');

        if($table === "all") {
            foreach($seeds as $seed) {
                $this->saveTableData($seed);
                echo $seed." executed successfully";
                echo "<br>";
            }
        } else {
            if(in_array($table, $seeds)) {
                $this->saveTableData($table);
            }
        }
        
        //Administrator
        $count = \App\Models\Administrator::count();
        if($count == 0) {
            \App\Models\Administrator::create([
                'email' => 'super@eventbuizz.com',
                'first_name' => 'Kashif',
                'last_name' => 'Idris',
                'password' => \Hash::make('123456'),
                'status' => 1
            ]); 
        }
        
        echo "All Done";
    }
    
    /**
     * saveTableData
     *
     * @param  mixed $seed
     * @return void
     */
    public function saveTableData($seed) {

        if(\Schema::hasTable($seed)) {
                    
            // Get table data from production
            $client = new \GuzzleHttp\Client(['base_uri' => config('setting.seeding_endpoint')]);

            $response = $client->request('GET', '/api/get-table-data/'.$seed, [
                "headers" => [
                    'Content-Type'      => 'application/json'
                ]
            ]);

            $response = json_decode($response->getBody());
           
            if(count((array) $response->results) > 0) {

                \DB::table($seed)->delete();  

                $collection = collect((array) $response->results);

                foreach($collection->chunk(20) as $chunk) {
                    foreach($chunk as $data) {
                        $insert = (array) $data;
                        if(array_key_exists("created_at", $insert)) {
                            $insert['created_at'] = \Carbon\Carbon::now();
                        }
                        if(array_key_exists("updated_at", $insert)) {
                            $insert['updated_at'] = \Carbon\Carbon::now();
                        }
                        \DB::table($seed)->insert($insert);
                    }
                }
            }
        }

    }
}
