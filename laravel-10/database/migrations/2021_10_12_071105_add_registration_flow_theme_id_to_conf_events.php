<?php
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegistrationFlowThemeIdToConfEvents extends Migration
{
    const TABLE = 'conf_events';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('registration_flow_theme_id')->index('registration_flow_theme_id')->default(1)->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('registration_flow_theme_id')->index('registration_flow_theme_id')->default(1)->nullable();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('registration_flow_theme_id');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('registration_flow_theme_id');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
