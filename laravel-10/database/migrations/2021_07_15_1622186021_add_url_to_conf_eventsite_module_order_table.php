<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUrlToConfEventsiteModuleOrderTable extends Migration
{
    const TABLE = 'conf_eventsite_modules_order';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->text('url');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->text('url');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('url');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('url');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
