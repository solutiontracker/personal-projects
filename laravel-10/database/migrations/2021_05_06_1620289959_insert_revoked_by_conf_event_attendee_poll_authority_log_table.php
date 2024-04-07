<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class InsertRevokedByConfEventAttendeePollAuthorityLogTable extends Migration
{
    const TABLE = 'conf_event_attendee_poll_authority_log';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('revoked_by')->nullable()->unsigned()->index()->after('is_read_from');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('revoked_by')->nullable()->unsigned()->index()->after('is_read_from');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('revoked_by');
        });
        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('revoked_by');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
