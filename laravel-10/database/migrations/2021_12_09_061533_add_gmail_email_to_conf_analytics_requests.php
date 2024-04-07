<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGmailEmailToConfAnalyticsRequests extends Migration
{
    const TABLE = 'conf_analytics_requests';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('gmail_email')->after('organizer_name');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('gmail_email')->after('organizer_name');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('gmail_email');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('gmail_email');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
