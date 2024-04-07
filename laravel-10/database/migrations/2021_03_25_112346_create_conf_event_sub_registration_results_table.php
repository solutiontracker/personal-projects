<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventSubRegistrationResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_sub_registration_results';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->text('answer');
            $table->bigInteger('answer_id')->index('answer_id');
            $table->text('comments')->nullable();
            $table->bigInteger('event_id')->index('event_id');
            $table->bigInteger('sub_registration_id')->index('sub_registration_id');
            $table->bigInteger('question_id')->index('question_id');
            $table->bigInteger('attendee_id')->index('attendee_id');
            $table->enum('answer_type', ['a', 'b'])->default('a')->index('answer_type')->comment('a=after payment, b= before payment');
            $table->tinyInteger('is_updated')->default(1);
            $table->integer('update_itration')->default(0);
            $table->tinyInteger('result_clear_admin')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->text('answer');
                $table->bigInteger('answer_id')->index('answer_id');
                $table->text('comments')->nullable();
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('sub_registration_id')->index('sub_registration_id');
                $table->bigInteger('question_id')->index('question_id');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->enum('answer_type', ['a', 'b'])->default('a')->index('answer_type')->comment('a=after payment, b= before payment');
                $table->tinyInteger('is_updated')->default(1);
                $table->integer('update_itration')->default(0);
                $table->tinyInteger('result_clear_admin')->default(0);
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
