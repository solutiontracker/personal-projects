<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventAttendeeSurveyQuestionsTable extends Migration
    {
        const TABLE = 'conf_event_attendee_survey_questions';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->enum('question_type', ['single', 'multiple', 'open', 'date', 'date_time', 'number', 'dropdown', 'matrix']);
                $table->enum('result_chart_type', ['pie', 'horizontal', 'vertical', 'progress']);
                $table->enum('anonymous', ['1', '0'])->default('0');
                $table->enum('required_question', ['1', '0'])->default('0');
                $table->enum('enable_comments', ['1', '0'])->default('0');
                $table->enum('is_anonymous', ['0', '1'])->default('0');
                $table->tinyInteger('max_options')->default(0);
                $table->integer('sort_order');
                $table->dateTime('start_date');
                $table->dateTime('end_date');
                $table->bigInteger('survey_id')->index('survey_id');
                $table->tinyInteger('status');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->enum('question_type', ['single', 'multiple', 'open', 'date', 'date_time', 'number', 'dropdown', 'matrix']);
                    $table->enum('result_chart_type', ['pie', 'horizontal', 'vertical', 'progress']);
                    $table->enum('anonymous', ['1', '0'])->default('0');
                    $table->enum('required_question', ['1', '0'])->default('0');
                    $table->enum('enable_comments', ['1', '0'])->default('0');
                    $table->enum('is_anonymous', ['0', '1'])->default('0');
                    $table->tinyInteger('max_options')->default(0);
                    $table->integer('sort_order');
                    $table->dateTime('start_date');
                    $table->dateTime('end_date');
                    $table->bigInteger('survey_id')->index('survey_id');
                    $table->tinyInteger('status');
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
