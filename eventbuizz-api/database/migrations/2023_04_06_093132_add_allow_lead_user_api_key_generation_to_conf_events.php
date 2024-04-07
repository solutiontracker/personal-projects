<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddAllowLeadUserApiKeyGenerationToConfEvents extends Migration
{
    const TABLE = 'conf_events';

    /**
     * Run the migrations.
     *allow_lead_user_api_key_generation
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->boolean('allow_lead_user_api_key_generation')->default(0);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->boolean('allow_lead_user_api_key_generation')->default(0);
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
            $table->dropColumn('allow_lead_user_api_key_generation');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('allow_lead_user_api_key_generation');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
