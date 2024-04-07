<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class InsertDateColumnsConfEventAttendeePollAuthorityLogTable extends Migration
{
    const TABLE = 'conf_event_attendee_poll_authority_log';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->timestamp('assign_date')->nullable()->after('revoked_by');
            $table->timestamp('is_accepted_date')->nullable()->after('assign_date');
            $table->timestamp('revoked_date')->nullable()->after('is_accepted_date');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->timestamp('assign_date')->nullable()->after('revoked_by');
                $table->timestamp('is_accepted_date')->nullable()->after('assign_date');
                $table->timestamp('revoked_date')->nullable()->after('is_accepted_date');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->timestamp('assign_date');
            $table->timestamp('is_accepted_date');
            $table->timestamp('revoked_date');
        });
        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->timestamp('assign_date');
                $table->timestamp('is_accepted_date');
                $table->timestamp('revoked_date');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
