<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfAlertNotificationLogTable extends Migration
{
    const TABLE = 'conf_alert_notification_log';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('organizer_id')->nullable();
            $table->bigInteger('event_id')->nullable();
            $table->bigInteger('attendee_id')->nullable();
            $table->bigInteger('alert_id')->nullable();
            $table->text('subject')->nullable();
            $table->string('to')->nullable();
            $table->string('from')->nullable();
            $table->string('type')->nullable();
            $table->integer('status')->nullable();
            $table->text('response')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('organizer_id')->nullable();
                $table->bigInteger('event_id')->nullable();
                $table->bigInteger('attendee_id')->nullable();
                $table->bigInteger('alert_id')->nullable();
                $table->text('subject')->nullable();
                $table->string('to')->nullable();
                $table->string('from')->nullable();
                $table->string('type')->nullable();
                $table->integer('status')->nullable();
                $table->text('response')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        EBSchema::dropDeleteTrigger(self::TABLE);
        Schema::dropIfExists(self::TABLE);
            Schema::connection(config('database.archive_connection'))->dropIfExists(self::TABLE);
    }
}
