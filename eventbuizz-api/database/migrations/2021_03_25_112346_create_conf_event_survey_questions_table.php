<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventSurveyQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_survey_questions';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->enum('question_type', ['single', 'multiple', 'open', 'number', 'date', 'date_time', 'dropdown', 'matrix'])->index('question_type');
            $table->enum('result_chart_type', ['pie', 'horizontal', 'vertical', 'progress'])->default('pie');
            $table->enum('anonymous', ['1', '0'])->default('0')->index('anonymous');
            $table->enum('required_question', ['1', '0'])->default('0')->index('required_question');
            $table->enum('enable_comments', ['1', '0'])->default('0');
            $table->tinyInteger('is_anonymous')->default(0)->index('is_anonymous');
            $table->integer('sort_order');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->bigInteger('survey_id')->index('survey_id');
            $table->tinyInteger('status')->nullable()->index('status');
            $table->tinyInteger('max_options');
            $table->integer('min_options')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->enum('question_type', ['single', 'multiple', 'open', 'number', 'date', 'date_time', 'dropdown', 'matrix'])->index('question_type');
                $table->enum('result_chart_type', ['pie', 'horizontal', 'vertical', 'progress'])->default('pie');
                $table->enum('anonymous', ['1', '0'])->default('0')->index('anonymous');
                $table->enum('required_question', ['1', '0'])->default('0')->index('required_question');
                $table->enum('enable_comments', ['1', '0'])->default('0');
                $table->tinyInteger('is_anonymous')->default(0)->index('is_anonymous');
                $table->integer('sort_order');
                $table->dateTime('start_date');
                $table->dateTime('end_date');
                $table->bigInteger('survey_id')->index('survey_id');
                $table->tinyInteger('status')->nullable()->index('status');
                $table->tinyInteger('max_options');
                $table->integer('min_options')->default(0);
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
