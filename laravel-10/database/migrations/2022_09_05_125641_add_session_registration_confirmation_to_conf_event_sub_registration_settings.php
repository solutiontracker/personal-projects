<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSessionRegistrationConfirmationToConfEventSubRegistrationSettings extends Migration
{

    const TABLE = 'conf_event_sub_registration_settings';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->longText('session_registration_confirmation')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->longText('session_registration_confirmation')->nullable();
            });
            
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('session_registration_confirmation');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('session_registration_confirmation');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

}
