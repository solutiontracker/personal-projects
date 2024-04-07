<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActivateJsonFeedToEventAgendasTable extends Migration
{
    const TABLE = 'conf_event_agendas';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('activate_json_feed')->default(0);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('activate_json_feed')->default(0);
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
            $table->dropColumn('activate_json_feed');
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table('conf_attendee_invites', function (Blueprint $table) {
                $table->dropColumn('activate_json_feed');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
