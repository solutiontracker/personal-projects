<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultipleColumnsToConfAttendeesRegistrationLog extends Migration
{
    const TABLE = 'conf_attendees_registration_log';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->unsignedBigInteger('register_by_id')->nullable()->index('register_by_id');
            $table->string('app_type')->nullable();
        });
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->unsignedBigInteger('register_by_id')->nullable()->index('register_by_id');
                $table->string('app_type')->nullable();
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
            $table->dropColumn('register_by_id');
            $table->dropColumn('app_type');
        });
        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('register_by_id');
                $table->dropColumn('app_type');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
