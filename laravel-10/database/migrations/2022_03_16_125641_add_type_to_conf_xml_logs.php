<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToConfXMLLOGS extends Migration
{

    const TABLE = 'conf_xml_log';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->enum('type', ['order', 'credit_note'])->nullable()->default('order');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->enum('type', ['order', 'credit_note'])->nullable()->default('order');
            });
            
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('type');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('type');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

}
