<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUseGroupToControlRequestToSpeakToConfEventTurnListSettings extends Migration
{
    const TABLE = 'conf_event_turn_list_settings';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('use_group_to_control_request_to_speak')->default(0);

        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('use_group_to_control_request_to_speak')->default(0);
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('use_group_to_control_request_to_speak');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('use_group_to_control_request_to_speak');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
