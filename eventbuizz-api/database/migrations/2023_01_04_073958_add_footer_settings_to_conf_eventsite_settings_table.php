<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddFooterSettingsToConfEventsiteSettingsTable extends Migration
{
    const TABLE = 'conf_eventsite_settings';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->boolean('use_reg_form_footer')->default(1);
            $table->string('reg_site_footer_image')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->boolean('use_reg_form_footer')->default(1);
                $table->string('reg_site_footer_image')->nullable();
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
            $table->dropColumn('use_reg_form_footer');
            $table->dropColumn('reg_site_footer_image');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('use_reg_form_footer');
                $table->dropColumn('reg_site_footer_image');
            });
            
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
