<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfFormBuilderFormResultTable extends Migration
{
    const TABLE = 'conf_form_builder_form_result';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->index();
            $table->foreignId('registration_form_id')->index();
            $table->foreignId('order_id')->index();
            $table->foreignId('attendee_id')->index();
            $table->foreignId('event_id')->index();
            $table->foreignId('section_id')->index();
            $table->foreignId('question_id')->index();
            $table->foreignId('answer_id')->index()->nullable()->default(null);
            $table->foreignId('grid_question_id')->index()->nullable()->default(null);
            $table->string('question_type')->nullable()->default(null);
            $table->longText('answer_value')->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();
        });
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->id();
                $table->foreignId('form_id')->index();
                $table->foreignId('registration_form_id')->index();
                $table->foreignId('order_id')->index();
                $table->foreignId('attendee_id')->index();
                $table->foreignId('event_id')->index();
                $table->foreignId('section_id')->index();
                $table->foreignId('question_id')->index();
                $table->foreignId('answer_id')->index()->nullable()->default(null);
                $table->foreignId('grid_question_id')->index()->nullable()->default(null);
                $table->string('question_type')->nullable()->default(null);
                $table->longText('answer_value')->nullable()->default(null);
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
