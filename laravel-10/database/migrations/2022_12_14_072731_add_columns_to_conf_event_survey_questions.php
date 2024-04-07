<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToConfEventSurveyQuestions extends Migration
{
    const TABLE = 'conf_event_survey_questions';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('is_participants_multiple_times')->default(0);
            $table->bigInteger('entries_per_participant')->nullable();
            DB::statement("ALTER TABLE conf_event_survey_questions CHANGE COLUMN question_type question_type ENUM('single','multiple','open','number','date','date_time','dropdown','matrix','world_cloud')  NULL ");


        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('is_participants_multiple_times')->default(0);
                $table->bigInteger('entries_per_participant')->nullable();
                DB::statement("ALTER TABLE conf_event_poll_questions CHANGE COLUMN question_type question_type ENUM('single','multiple','open','number','date','date_time','dropdown','matrix','world_cloud')  NULL ");

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
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('is_participants_multiple_times');
            $table->dropColumn('entries_per_participant');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('is_participants_multiple_times');
                $table->dropColumn('entries_per_participant');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
