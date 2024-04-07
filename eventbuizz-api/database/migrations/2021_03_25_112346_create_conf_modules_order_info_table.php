<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfModulesOrderInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_modules_order_info';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->index('name');
            $table->string('value');
            $table->bigInteger('languages_id')->index('languages_id_2');
            $table->tinyInteger('status')->index('status');
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('module_order_id')->index('module_order_id_2');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->string('name')->index('name');
                $table->string('value');
                $table->bigInteger('languages_id')->index('languages_id_2');
                $table->tinyInteger('status')->index('status');
                $table->timestamps();
            $table->softDeletes();
                $table->bigInteger('module_order_id')->index('module_order_id_2');
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
