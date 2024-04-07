<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddLanguageColToConfTemplatesMaster extends Migration
{

    const TABLE = "conf_templates_master";

    /**
     * Run the migrations.migrations
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('language_id')->index('language_id')->default(1);
        });

        //on the live server
        if (app()->environment('live')) {
            //create the same field in the archive database
            //that is our backup database for live
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('language_id')->index('language_id')->default(1);
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
            $table->dropColumn('language_id');
        });

        //on the live environment
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('language_id');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
