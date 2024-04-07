<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrintMessageToConfPrintSettings extends Migration
{
    const TABLE = 'conf_print_settings';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('print_message', 500)->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('print_message', 500)->nullable();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('print_message');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('print_message');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
