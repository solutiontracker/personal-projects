<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSqsQueueConnectionToConfEventTurnListSettings extends Migration
{
    const TABLE = 'conf_event_turn_list_settings';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('sqs_queue_connection')->default(0);
            $table->string('current_queue')->nullable();

        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('sqs_queue_connection')->default(0);
                $table->string('current_queue')->nullable();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('sqs_queue_connection');
            $table->dropColumn('current_queue');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('sqs_queue_connection');
                $table->dropColumn('current_queue');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
