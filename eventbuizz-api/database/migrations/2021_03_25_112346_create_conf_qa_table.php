<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfQaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_qa';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->string('answered');
            $table->tinyInteger('show_projector');
            $table->tinyInteger('rejected');
            $table->dateTime('q_startTime');
            $table->tinyInteger('isStart');
            $table->tinyInteger('displayed');
            $table->tinyInteger('sort_order');
            $table->bigInteger('attendee_id')->index('attendee_id');
            $table->bigInteger('event_id')->index('event_id');
            $table->bigInteger('agenda_id')->index('agenda_id');
            $table->integer('speaker_id');
            $table->tinyInteger('anonymous_user')->default(0);
            $table->integer('like_count');
            $table->tinyInteger('clone')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->string('answered');
                $table->tinyInteger('show_projector');
                $table->tinyInteger('rejected');
                $table->dateTime('q_startTime');
                $table->tinyInteger('isStart');
                $table->tinyInteger('displayed');
                $table->tinyInteger('sort_order');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('agenda_id')->index('agenda_id');
                $table->integer('speaker_id');
                $table->tinyInteger('anonymous_user')->default(0);
                $table->integer('like_count');
                $table->tinyInteger('clone')->default(0);
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
