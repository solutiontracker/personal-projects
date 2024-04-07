<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddActionIpUserAgentToConfAttendeesRegistrationLog extends Migration
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
            $table->string('model')->after('attendee_id')->nullable();
            $table->string('action')->nullable();
            $table->string('ip')->nullable();
            $table->string('user_agent')->nullable();
            DB::statement("ALTER TABLE conf_attendees_registration_log MODIFY COLUMN register_by 
                ENUM('admin', 'front', 'autoregister', 'attendee', 'hub_admin', 'sale_agent', 'organizer')");

        });
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('model')->after('attendee_id')->nullable();
                $table->string('action')->nullable();
                $table->string('ip')->nullable();
                $table->string('user_agent')->nullable();
                DB::statement("ALTER TABLE conf_attendees_registration_log MODIFY COLUMN register_by 
                ENUM('admin', 'front', 'autoregister', 'attendee', 'hub_admin', 'sale_agent', 'organizer')");

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
            $table->dropColumn('action');
            $table->dropColumn('model');
            $table->dropColumn('ip');
            $table->dropColumn('user_agent');
        });
        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('action');
                $table->dropColumn('model');
                $table->dropColumn('ip');
                $table->dropColumn('user_agent');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
