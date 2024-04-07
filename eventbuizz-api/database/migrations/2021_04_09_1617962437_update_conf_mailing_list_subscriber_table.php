<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;
class UpdateConfMailingListSubscriberTable extends Migration
{
    const TABLE = 'conf_mailing_list_subscriber';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('event_id')->default(0)->index()->after('organizer_id');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('event_id')->default(0)->index()->after('organizer_id');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('event_id');
        });
        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('event_id');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
