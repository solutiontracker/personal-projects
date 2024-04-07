<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublishShowInAppAndShowInSiteToConfInformationSections extends Migration
{

    const TABLE = 'conf_information_sections';


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('show_in_app')->default(0);
            $table->tinyInteger('show_in_reg_site')->default(0);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('show_in_app')->default(0);
                $table->tinyInteger('show_in_reg_site')->default(0);
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
            $table->dropColumn('show_in_app');
            $table->dropColumn('show_in_reg_site');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('show_in_app');
                $table->dropColumn('show_in_reg_site');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
