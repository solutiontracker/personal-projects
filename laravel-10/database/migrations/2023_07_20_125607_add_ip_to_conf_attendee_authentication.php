<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddIpToConfAttendeeAuthentication extends Migration
{
    const TABLE = 'conf_attendee_authentication';

    /**
     * Run the migrations.
     *
     * @return void
    */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('ip',30)->nullable();
            $table->string('user_agent',255)->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('ip',30)->nullable();
                $table->string('user_agent',255)->nullable();
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
            $table->dropColumn('ip');
            $table->dropColumn('user_agent');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('ip');
                $table->dropColumn('user_agent');
            });
            
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
