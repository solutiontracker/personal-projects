<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEanNumberToConfEventsTable extends Migration
{
    const TABLE = 'conf_events';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('ean_number')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('ean_number')->nullable();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('ean_number');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('ean_number');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
