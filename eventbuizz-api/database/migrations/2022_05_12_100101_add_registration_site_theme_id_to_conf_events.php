<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddRegistrationSiteThemeIdToConfEvents extends Migration
{
    const TABLE = 'conf_events';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->foreignId('registration_site_theme_id')->default(1);
            $table->foreignId('registration_site_layout_id')->default(1);
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->foreignId('registration_site_theme_id')->default(1);
                $table->foreignId('registration_site_layout_id')->default(1);
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
            $table->dropColumn('registration_site_theme_id');
            $table->dropColumn('registration_site_layout_id');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('registration_site_theme_id');
                $table->dropColumn('registration_site_layout_id');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
