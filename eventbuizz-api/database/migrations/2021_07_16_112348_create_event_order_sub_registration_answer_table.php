<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventOrderSubRegistrationAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_order_sub_registration_answer';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->integer('question_id');
            $table->integer('answer_id');
            $table->integer('matrix_id');
            $table->integer('attendee_id');
            $table->text('comment');
            $table->text('answer');
            $table->timestamps();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('order_id');
                $table->integer('question_id');
                $table->integer('answer_id');
                $table->integer('matrix_id');
                $table->integer('attendee_id');
                $table->text('comment');
                $table->text('answer');
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
