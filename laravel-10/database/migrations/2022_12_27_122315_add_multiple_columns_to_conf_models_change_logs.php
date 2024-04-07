<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultipleColumnsToConfModelsChangeLogs extends Migration
{
    const TABLE = 'conf_models_change_logs';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('app_type')->nullable()->default('event_center');
            $table->unsignedBigInteger('logged_by_id')->nullable()->index('register_by_id');
            $table->string('logged_by_user_type')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('app_type')->nullable()->default('event_center');
                $table->unsignedBigInteger('logged_by_id')->nullable()->index('register_by_id');
                $table->string('logged_by_user_type')->nullable();
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
            $table->dropColumn('app_type');
            $table->dropColumn('logged_by_id');
            $table->dropColumn('logged_by_user_type');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('app_type');
                $table->dropColumn('logged_by_id');
                $table->dropColumn('logged_by_user_type');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
