<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class ChangeDefaultValueOfLeadUserWithoutContactPersonInConfLeadsSettings extends Migration
{
    const TABLE = 'conf_leads_settings';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table(self::TABLE, function (Blueprint $table) {
            DB::statement('ALTER TABLE `conf_leads_settings` CHANGE COLUMN `lead_user_without_contact_person` `lead_user_without_contact_person` TINYINT NOT NULL DEFAULT -1;');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                DB::statement('ALTER TABLE `conf_leads_settings` CHANGE COLUMN `lead_user_without_contact_person` `lead_user_without_contact_person` TINYINT NOT NULL DEFAULT -1;');
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
            $table->dropColumn('lead_user_without_contact_person');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('lead_user_without_contact_person');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
