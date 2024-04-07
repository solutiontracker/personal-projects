<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventAttendeeSurveyResultsTable extends Migration
    {
        const TABLE = 'conf_event_attendee_survey_results';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->text('answer');
                $table->text('comments');
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('survey_id')->index('survey_id');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->bigInteger('question_id')->index('question_id');
                $table->integer('answer_id')->index('answer_id');
                $table->tinyInteger('status')->index('status');
                $table->timestamps();
            $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->text('answer');
                    $table->text('comments');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->bigInteger('survey_id')->index('survey_id');
                    $table->bigInteger('attendee_id')->index('attendee_id');
                    $table->bigInteger('question_id')->index('question_id');
                    $table->integer('answer_id')->index('answer_id');
                    $table->tinyInteger('status')->index('status');
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
