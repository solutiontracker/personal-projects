<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddToCalenderShowToConfEventsiteSettings extends Migration
{
    const TABLE = 'conf_eventsite_settings';

    /**
     * Run the migrations.
     *
     * @return void 
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->boolean('calender_show')->default(1)->comment('Show Calender on registeration side');
        });
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->integer('calender_show')->default(1)->comment('Show Calender on registeration side');
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
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('calender_show');
        });
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('calender_show');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
