<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBackgroundAndHeaderColorToConfEventsiteSettings extends Migration
{
    const TABLE = 'conf_eventsite_settings';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('event_info_background_color')->nullable();
            $table->string('event_info_heading_color')->nullable();
        });

        if (app()->environment('live')) {
            
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('event_info_background_color')->nullable();
                $table->string('event_info_heading_color')->nullable();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('event_info_background_color');
            $table->dropColumn('event_info_heading_color');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('event_info_background_color');
                $table->dropColumn('event_info_heading_color');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
