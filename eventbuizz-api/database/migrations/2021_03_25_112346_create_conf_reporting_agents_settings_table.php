<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfReportingAgentsSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_reporting_agents_settings';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('organizer_id');
            $table->tinyInteger('order_number')->default(1);
            $table->tinyInteger('order_date')->default(1);
            $table->tinyInteger('name_email')->default(1);
            $table->tinyInteger('job_title')->default(1);
            $table->tinyInteger('company')->default(1);
            $table->tinyInteger('amount')->default(1);
            $table->tinyInteger('sales_agent')->default(1);
            $table->tinyInteger('order_status')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('organizer_id');
                $table->tinyInteger('order_number')->default(1);
                $table->tinyInteger('order_date')->default(1);
                $table->tinyInteger('name_email')->default(1);
                $table->tinyInteger('job_title')->default(1);
                $table->tinyInteger('company')->default(1);
                $table->tinyInteger('amount')->default(1);
                $table->tinyInteger('sales_agent')->default(1);
                $table->tinyInteger('order_status')->default(1);
                $table->timestamps();
                $table->softDeletes();
            });
            
	        EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        EBSchema::dropDeleteTrigger(self::TABLE);
        Schema::dropIfExists(self::TABLE);
            Schema::connection(config('database.archive_connection'))->dropIfExists(self::TABLE);
    }
}
