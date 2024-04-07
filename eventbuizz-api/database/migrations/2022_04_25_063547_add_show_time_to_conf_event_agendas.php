<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShowTimeToConfEventAgendas extends Migration
{
    const TABLE = 'conf_event_agendas';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('hide_time')->default(0)->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('hide_time')->default(0)->nullable();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('hide_time');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('hide_time');
            });

        }

        EBSchema::createBeforeDeleteTrigger(self::TABLE);
    }
}
