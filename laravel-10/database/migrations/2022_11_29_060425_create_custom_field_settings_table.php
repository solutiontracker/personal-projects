<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomFieldSettingsTable extends Migration
{
    const TABLE = "conf_custom_field_settings";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->tinyInteger('custom_field_mode')->default(0);
            $table->integer('event_id');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection('mysql_archive')->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->tinyInteger('custom_field_mode')->default(0);
                $table->integer('event_id');
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