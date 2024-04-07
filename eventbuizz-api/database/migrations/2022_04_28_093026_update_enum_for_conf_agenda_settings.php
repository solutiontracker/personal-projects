<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEnumForConfAgendaSettings extends Migration
{

    const TABLE = 'conf_agenda_settings';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            DB::statement("ALTER TABLE `conf_agenda_settings` CHANGE `program_view` `program_view` ENUM('default','vertical','horizontal','location_based_listing')");
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                DB::statement("ALTER TABLE `conf_agenda_settings` CHANGE `program_view` `program_view` ENUM('default','vertical','horizontal','location_based_listing')");
            });
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            DB::statement("ALTER TABLE `conf_agenda_settings` CHANGE `program_view` `program_view` ENUM('default','vertical','horizontal')");
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                DB::statement("ALTER TABLE `conf_agenda_settings` CHANGE `program_view` `program_view` ENUM('default','vertical','horizontal')");
            });

        }
    }
}
