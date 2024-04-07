<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventCheckedInAttendeesToSendTo extends Migration
{
    const TABLE = 'conf_event_alerts';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            \DB::statement("ALTER TABLE `conf_event_alerts` CHANGE `sendto` `sendto` ENUM('all','agendas','groups','individuals','workshops','polls','surveys','sponsors','exhibitors','attendee_type','event_checked_in_attendees', 'event_not_checked_in_attendees');");
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                \DB::statement("ALTER TABLE `conf_event_alerts` CHANGE `sendto` `sendto` ENUM('all','agendas','groups','individuals','workshops','polls','surveys','sponsors','exhibitors','attendee_type','event_checked_in_attendees', 'event_not_checked_in_attendees');");
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            \DB::statement("ALTER TABLE `conf_event_alerts` CHANGE `sendto` `sendto` ENUM('all','agendas','groups','individuals','workshops','polls','surveys','sponsors','exhibitors','attendee_type');");
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                \DB::statement("ALTER TABLE `conf_event_alerts` CHANGE `sendto` `sendto` ENUM('all','agendas','groups','individuals','workshops','polls','surveys','sponsors','exhibitors','attendee_type');");
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
