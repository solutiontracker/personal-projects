<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlotStartEndTimeAndDurationToConfSponsorsSettings extends Migration
{
    const TABLE = 'conf_sponsors_settings';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('duration')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->integer('duration')->nullable();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('start_time');
            $table->dropColumn('end_time');
            $table->dropColumn('duration');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('start_time');
                $table->dropColumn('end_time');
                $table->dropColumn('duration');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
