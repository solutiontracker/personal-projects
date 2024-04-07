<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddProjectorSettingsToConfEventCheckinSettings extends Migration
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
            $table->boolean('enable_event_projectors')->default(0);
            $table->boolean('enable_agenda_projectors')->default(0);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->boolean('enable_event_projectors')->default(0);
                $table->boolean('enable_agenda_projectors')->default(0);
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
            $table->dropColumn('enable_event_projectors');
            $table->dropColumn('enable_agenda_projectors');
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('enable_event_projectors');
                $table->dropColumn('enable_agenda_projectors');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
