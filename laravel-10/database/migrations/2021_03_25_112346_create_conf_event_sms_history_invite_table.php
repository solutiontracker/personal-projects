<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventSmsHistoryInviteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_sms_history_invite';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('organizer_id')->index('organizer_id');
            $table->bigInteger('event_id')->index('event_id');
            $table->string('name', 250)->nullable();
            $table->string('email', 250)->nullable();
            $table->string('phone', 100);
            $table->tinyInteger('status');
            $table->text('status_msg');
            $table->text('sms');
            $table->timestamps();
            $table->softDeletes();
            $table->enum('type', ['attendee_invite', 'attendee_reminder']);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('organizer_id')->index('organizer_id');
                $table->bigInteger('event_id')->index('event_id');
                $table->string('name', 250)->nullable();
                $table->string('email', 250)->nullable();
                $table->string('phone', 100);
                $table->tinyInteger('status');
                $table->text('status_msg');
                $table->text('sms');
                $table->timestamps();
            $table->softDeletes();
                $table->enum('type', ['attendee_invite', 'attendee_reminder']);
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
