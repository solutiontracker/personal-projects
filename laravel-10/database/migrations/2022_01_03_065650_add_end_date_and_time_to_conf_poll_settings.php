<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEndDateAndTimeToConfPollSettings extends Migration
{
    const TABLE = 'conf_poll_settings';


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->date('end_date')->default('0000-00-00');
            $table->time('end_time')->default('00:00:00');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->date('end_date')->default('0000-00-00');
                $table->time('end_time')->default('00:00:00');
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
            $table->dropColumn('end_date');
            $table->dropColumn('end_time');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('end_date');
                $table->dropColumn('end_time');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
