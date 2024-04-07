<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValidateAttendeeInviteWithDomainToConfAttendeeSettingsTable extends Migration
{
    
    const TABLE = 'conf_attendee_settings';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('validate_attendee_invite_with_domain')->default(0);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('validate_attendee_invite_with_domain')->default(0);
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
            $table->dropColumn('validate_attendee_invite_with_domain');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('validate_attendee_invite_with_domain');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
