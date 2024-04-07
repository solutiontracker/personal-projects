<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class ChangeDefaultValueOfLeadPortalSettingsInConfExhibitorsSettings extends Migration
{
    const TABLE = 'conf_exhibitors_settings';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            DB::statement('ALTER TABLE `conf_exhibitors_settings` CHANGE COLUMN `show_leads` `show_leads` TINYINT NOT NULL DEFAULT 0;');
            DB::statement('ALTER TABLE `conf_exhibitors_settings` CHANGE COLUMN `catalogue_product` `catalogue_product` TINYINT NOT NULL DEFAULT 0;');
            DB::statement('ALTER TABLE `conf_exhibitors_settings` CHANGE COLUMN `consent_management` `consent_management` TINYINT NOT NULL DEFAULT 0;');
            DB::statement('ALTER TABLE `conf_exhibitors_settings` CHANGE COLUMN `show_download_lead_app` `show_download_lead_app` TINYINT NOT NULL DEFAULT 0;');
            DB::statement('ALTER TABLE `conf_exhibitors_settings` CHANGE COLUMN `show_template` `show_template` TINYINT NOT NULL DEFAULT 0;');
            DB::statement('ALTER TABLE `conf_exhibitors_settings` CHANGE COLUMN `show_lead_scan_confirmation` `show_lead_scan_confirmation` TINYINT NOT NULL DEFAULT 0;');
            DB::statement('ALTER TABLE `conf_exhibitors_settings` CHANGE COLUMN `show_lead_user_promotion` `show_lead_user_promotion` TINYINT NOT NULL DEFAULT 0;');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                DB::statement('ALTER TABLE `conf_exhibitors_settings` CHANGE COLUMN `show_leads` `show_leads` TINYINT NOT NULL DEFAULT 0;');
                DB::statement('ALTER TABLE `conf_exhibitors_settings` CHANGE COLUMN `catalogue_product` `catalogue_product` TINYINT NOT NULL DEFAULT 0;');
                DB::statement('ALTER TABLE `conf_exhibitors_settings` CHANGE COLUMN `consent_management` `consent_management` TINYINT NOT NULL DEFAULT 0;');
                DB::statement('ALTER TABLE `conf_exhibitors_settings` CHANGE COLUMN `show_download_lead_app` `show_download_lead_app` TINYINT NOT NULL DEFAULT 0;');
                DB::statement('ALTER TABLE `conf_exhibitors_settings` CHANGE COLUMN `show_template` `show_template` TINYINT NOT NULL DEFAULT 0;');
                DB::statement('ALTER TABLE `conf_exhibitors_settings` CHANGE COLUMN `show_lead_scan_confirmation` `show_lead_scan_confirmation` TINYINT NOT NULL DEFAULT 0;');
                DB::statement('ALTER TABLE `conf_exhibitors_settings` CHANGE COLUMN `show_lead_user_promotion` `show_lead_user_promotion` TINYINT NOT NULL DEFAULT 0;');
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
            $table->dropColumn('show_leads');
            $table->dropColumn('catalogue_product');
            $table->dropColumn('consent_management');
            $table->dropColumn('show_download_lead_app');
            $table->dropColumn('show_template');
            $table->dropColumn('show_lead_scan_confirmation');
            $table->dropColumn('show_lead_user_promotion');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('show_leads');
                $table->dropColumn('catalogue_product');
                $table->dropColumn('consent_management');
                $table->dropColumn('show_download_lead_app');
                $table->dropColumn('show_template');
                $table->dropColumn('show_lead_scan_confirmation');
                $table->dropColumn('show_lead_user_promotion');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
