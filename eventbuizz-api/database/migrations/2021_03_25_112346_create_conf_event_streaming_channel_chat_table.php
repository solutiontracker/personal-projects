<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventStreamingChannelChatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_streaming_channel_chat';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('event_id')->nullable()->index('event_id');
            $table->integer('agenda_id')->nullable()->index('agenda_id');
            $table->integer('attendee_id')->nullable()->index('attendee_id');
            $table->text('message')->nullable();
            $table->integer('organizer_id')->nullable()->index('organizer_id');
            $table->string('ChannelName')->nullable();
            $table->enum('sendBy', ['organizer', 'attendee', '', ''])->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('event_id')->nullable()->index('event_id');
                $table->integer('agenda_id')->nullable()->index('agenda_id');
                $table->integer('attendee_id')->nullable()->index('attendee_id');
                $table->text('message')->nullable();
                $table->integer('organizer_id')->nullable()->index('organizer_id');
                $table->string('ChannelName')->nullable();
                $table->enum('sendBy', ['organizer', 'attendee', '', ''])->nullable();
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
