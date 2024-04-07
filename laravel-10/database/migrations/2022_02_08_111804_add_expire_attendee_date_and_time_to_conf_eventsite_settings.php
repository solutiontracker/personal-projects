<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpireAttendeeDateAndTimeToConfEventsiteSettings extends Migration
{
    const TABLE = 'conf_eventsite_settings';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dateTime('not_attending_expiry_date')->default('0000-00-00 00:00:00');
            $table->time('not_attending_expiry_time')->default('00:00:00');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dateTime('not_attending_expiry_date')->default('0000-00-00 00:00:00');
                $table->time('not_attending_expiry_time')->default('00:00:00');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('not_attending_expiry_date');
            $table->dropColumn('not_attending_expiry_time');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('not_attending_expiry_date');
                $table->dropColumn('not_attending_expiry_time');
            });

        }
    }
}
