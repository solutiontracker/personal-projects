<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddSurveyFormLeadAppToConfLeadsSettings extends Migration
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
            $table->tinyInteger('attendees_surveys')->default(0);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('attendees_surveys')->default(0);
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
            $table->dropColumn('attendees_surveys');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('attendees_surveys');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
