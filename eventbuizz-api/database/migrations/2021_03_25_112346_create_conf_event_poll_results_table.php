<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventPollResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_poll_results';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->text('answer');
            $table->text('comments')->nullable();
            $table->bigInteger('question_id')->index('question_id');
            $table->bigInteger('answer_id')->index('answer_id');
            $table->bigInteger('event_id')->index('event_id');
            $table->bigInteger('poll_id')->index('poll_id');
            $table->bigInteger('agenda_id')->index('agenda_id');
            $table->bigInteger('attendee_id')->index('attendee_id');
            $table->tinyInteger('is_updated')->default(1);
            $table->tinyInteger('status')->nullable()->index('status');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

	        Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->text('answer');
                $table->text('comments')->nullable();
                $table->bigInteger('question_id')->index('question_id');
                $table->bigInteger('answer_id')->index('answer_id');
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('poll_id')->index('poll_id');
                $table->bigInteger('agenda_id')->index('agenda_id');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->tinyInteger('is_updated')->default(1);
                $table->tinyInteger('status')->nullable()->index('status');
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
