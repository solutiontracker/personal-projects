<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShowProgramOnCheckInAppToConfEventAgendas extends Migration
{
    const TABLE = 'conf_event_agendas';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('show_program_on_check_in_app')->default(0);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('show_program_on_check_in_app')->default(0);
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('show_program_on_check_in_app');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('show_program_on_check_in_app');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
