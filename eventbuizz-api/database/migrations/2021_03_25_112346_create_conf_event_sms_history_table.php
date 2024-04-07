<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventSmsHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_sms_history';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('type');
            $table->text('status_msg');
            $table->text('sms');
            $table->decimal('cost', 11)->nullable();
            $table->integer('smsAmount')->index('smsAmount');
            $table->string('coverage', 15);
            $table->bigInteger('event_id')->index('event_id');
            $table->bigInteger('organizer_id')->index('organizer_id');
            $table->bigInteger('attendee_id')->index('attendee_id');
            $table->tinyInteger('status')->index('status');
            $table->integer('sent_id')->index('sent_id');
            $table->dateTime('date_sent');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->string('name');
                $table->string('email');
                $table->string('phone');
                $table->string('type');
                $table->text('status_msg');
                $table->text('sms');
                $table->decimal('cost', 11)->nullable();
                $table->integer('smsAmount')->index('smsAmount');
                $table->string('coverage', 15);
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('organizer_id')->index('organizer_id');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->tinyInteger('status')->index('status');
                $table->integer('sent_id')->index('sent_id');
                $table->dateTime('date_sent');
                $table->timestamps();
                $table->softDeletes();
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
        EBSchema::dropDeleteTrigger(self::TABLE);
        Schema::dropIfExists(self::TABLE);
            Schema::connection(config('database.archive_connection'))->dropIfExists(self::TABLE);
    }
}
