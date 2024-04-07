<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfAttendeeReplaceDelegateLogs extends Migration
{
    const TABLE = 'conf_attendee_replace_delegate_logs';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->index('event_id');
            $table->unsignedBigInteger('organizer_id')->index('organizer_id');
            $table->unsignedBigInteger('replaced_attendee_id')->index('replaced_attendee_id');
            $table->unsignedBigInteger('replace_by_attendee_id')->index('replace_by_attendee_id');
            $table->string('delegate_number');
            $table->dateTime('date_time');
            $table->text('description');
            $table->timestamps();
        });
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id')->index('event_id');
                $table->unsignedBigInteger('organizer_id')->index('organizer_id');
                $table->unsignedBigInteger('replaced_attendee_id')->index('replaced_attendee_id');
                $table->unsignedBigInteger('replace_by_attendee_id')->index('replace_by_attendee_id');
                $table->string('delegate_number');
                $table->dateTime('date_time');
                $table->text('description');
                $table->timestamps();
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
