<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataColumnToConfEventNotifications extends Migration
{
    const TABLE = 'conf_event_notifications';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->longText('data')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->longText('data')->nullable();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('data');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('data');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
