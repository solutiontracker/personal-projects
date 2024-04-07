<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHideFromEventHistoryToConfEvents extends Migration
{
    const TABLE = 'conf_events';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('hide_from_event_history')->default(0);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('hide_from_event_history')->default(0);
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
            $table->dropColumn('hide_from_event_history');
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table('conf_attendee_invites', function (Blueprint $table) {
                $table->dropColumn('hide_from_event_history');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
