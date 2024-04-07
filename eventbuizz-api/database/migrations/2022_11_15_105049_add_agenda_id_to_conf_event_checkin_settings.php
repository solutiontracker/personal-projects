<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAgendaIdToConfEventCheckinSettings extends Migration
{
    const TABLE = 'conf_event_checkin_settings';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('agenda_id')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('agenda_id')->nullable();
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
            $table->dropColumn('agenda_id');
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table('conf_attendee_invites', function (Blueprint $table) {
                $table->dropColumn('agenda_id');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
