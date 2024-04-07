<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultipleColumnsToConfAttendeeChangeLog extends Migration
{
    const TABLE = 'conf_attendee_change_log';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->unsignedBigInteger('logged_by_id')->nullable()->index('logged_by_id');
            $table->string('logged_by_user_type')->nullable();
            $table->string('action_model')->nullable();
            $table->string('action')->nullable();
            $table->string('app_type')->nullable()->default('event_center');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->unsignedBigInteger('logged_by_id')->nullable()->index('logged_by_id');
                $table->string('logged_by_user_type')->nullable();
                $table->string('action_model')->nullable();
                $table->string('action')->nullable();
                $table->string('app_type')->nullable()->default('event_center');
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
            $table->dropColumn('logged_by_id');
            $table->dropColumn('logged_by_user_type');
            $table->dropColumn('action');
            $table->dropColumn('action_model');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('logged_by_id');
                $table->dropColumn('logged_by_user_type');
                $table->dropColumn('action');
                $table->dropColumn('action_model');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
