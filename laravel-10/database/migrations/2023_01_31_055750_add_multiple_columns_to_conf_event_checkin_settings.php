<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultipleColumnsToConfEventCheckinSettings extends Migration
{
    const TABLE='conf_event_checkin_settings';
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('show_event_checkin_history')->nullable()->default(1);
            $table->tinyInteger('show_programs_checkin_history')->nullable()->default(1);
            $table->tinyInteger('show_groups_checkin_history')->nullable()->default(1);
            $table->tinyInteger('show_tickets_checkin_history')->nullable()->default(1);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('show_event_checkin_history')->nullable()->default(1);
                $table->tinyInteger('show_programs_checkin_history')->nullable()->default(1);
                $table->tinyInteger('show_groups_checkin_history')->nullable()->default(1);
                $table->tinyInteger('show_tickets_checkin_history')->nullable()->default(1);
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
            $table->dropColumn('show_event_checkin_history');
            $table->dropColumn('show_programs_checkin_history');
            $table->dropColumn('show_groups_checkin_history');
            $table->dropColumn('show_tickets_checkin_history');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('show_event_checkin_history');
                $table->dropColumn('show_programs_checkin_history');
                $table->dropColumn('show_groups_checkin_history');
                $table->dropColumn('show_tickets_checkin_history');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
