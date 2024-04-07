<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressInfoToConfSpeakerSettings extends Migration
{
    const TABLE = 'conf_speaker_settings';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('place_of_birth')->default(0);
            $table->tinyInteger('passport_no')->default(0);
            $table->tinyInteger('date_of_issue_passport')->default(0);
            $table->tinyInteger('date_of_expiry_passport')->default(0);
            $table->tinyInteger('pa_house_no')->default(0);
            $table->tinyInteger('pa_street')->default(0);
            $table->tinyInteger('pa_post_code')->default(0);
            $table->tinyInteger('pa_city')->default(0);
            $table->tinyInteger('pa_country')->default(0);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('place_of_birth')->default(0);
                $table->tinyInteger('passport_no')->default(0);
                $table->tinyInteger('date_of_issue_passport')->default(0);
                $table->tinyInteger('date_of_expiry_passport')->default(0);
                $table->tinyInteger('pa_house_no')->default(0);
                $table->tinyInteger('pa_street')->default(0);
                $table->tinyInteger('pa_post_code')->default(0);
                $table->tinyInteger('pa_city')->default(0);
                $table->tinyInteger('pa_country')->default(0);
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('place_of_birth');
            $table->dropColumn('passport_no');
            $table->dropColumn('date_of_issue_passport');
            $table->dropColumn('date_of_expiry_passport');
            $table->dropColumn('pa_house_no');
            $table->dropColumn('pa_street');
            $table->dropColumn('pa_post_code');
            $table->dropColumn('pa_city');
            $table->dropColumn('pa_country');

        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('place_of_birth');
                $table->dropColumn('passport_no');
                $table->dropColumn('date_of_issue_passport');
                $table->dropColumn('date_of_expiry_passport');
                $table->dropColumn('pa_house_no');
                $table->dropColumn('pa_street');
                $table->dropColumn('pa_post_code');
                $table->dropColumn('pa_city');
                $table->dropColumn('pa_country');
            });

        }

        EBSchema::createBeforeDeleteTrigger(self::TABLE);
    }
}
