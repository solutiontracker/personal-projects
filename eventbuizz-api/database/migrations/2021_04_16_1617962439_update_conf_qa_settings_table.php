<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class UpdateConfQaSettingsTable extends Migration
{
    const TABLE = 'conf_qa_settings';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('program_heading_text_color',255)->default('#262626');
            $table->string('program_heading_background_color',255)->default('#262626');
            $table->string('program_text_color',255)->default('#262626');
            $table->string('program_date_time_color',255)->default('#262626');
            $table->string('program_icon_color',255)->default('#262626');
            $table->string('program_description_color',255)->default('#262626');
            $table->string('program_detail_background_color',255)->default('#FFF');
            $table->string('active_question_attendee_detail_color',255)->default('#FFF');
            $table->string('active_question_text_color',255)->default('#FFF');
            $table->string('active_question_speaker_heading_background_color',255)->default('#FFF');
            $table->string('active_question_speaker_heading_text_color',255)->default('#F38330');
            $table->string('active_question_speaker_listing_text_color',255)->default('#FFF');
            $table->string('active_question_icon_color',255)->default('#FFF');
            $table->string('active_question_like_count_color',255)->default('#FFF');
            $table->string('active_question_box_background_color',255)->default('#F38330');
            $table->string('question_attendee_detail_color',255)->default('#262626');
            $table->string('question_text_color',255)->default('#262626');
            $table->string('question_speaker_heading_background_color',255)->default('#0067A2');
            $table->string('question_speaker_heading_text_color',255)->default('#FFF');
            $table->string('question_speaker_listing_text_color',255)->default('#262626');
            $table->string('question_icon_color',255)->default('#262626');
            $table->string('question_like_count_color',255)->default('#262626');
            $table->string('question_box_background_color',255)->default('#FFF');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('program_heading_text_color',255)->default('#262626');
                $table->string('program_heading_background_color',255)->default('#262626');
                $table->string('program_text_color',255)->default('#262626');
                $table->string('program_date_time_color',255)->default('#262626');
                $table->string('program_icon_color',255)->default('#262626');
                $table->string('program_description_color',255)->default('#262626');
                $table->string('program_detail_background_color',255)->default('#FFF');
                $table->string('active_question_attendee_detail_color',255)->default('#FFF');
                $table->string('active_question_text_color',255)->default('#FFF');
                $table->string('active_question_speaker_heading_background_color',255)->default('#FFF');
                $table->string('active_question_speaker_heading_text_color',255)->default('#F38330');
                $table->string('active_question_speaker_listing_text_color',255)->default('#FFF');
                $table->string('active_question_icon_color',255)->default('#FFF');
                $table->string('active_question_like_count_color',255)->default('#FFF');
                $table->string('active_question_box_background_color',255)->default('#F38330');
                $table->string('question_attendee_detail_color',255)->default('#262626');
                $table->string('question_text_color',255)->default('#262626');
                $table->string('question_speaker_heading_background_color',255)->default('#0067A2');
                $table->string('question_speaker_heading_text_color',255)->default('#FFF');
                $table->string('question_speaker_listing_text_color',255)->default('#262626');
                $table->string('question_icon_color',255)->default('#262626');
                $table->string('question_like_count_color',255)->default('#262626');
                $table->string('question_box_background_color',255)->default('#FFF');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('program_heading_text_color');
            $table->dropColumn('program_heading_background_color');
            $table->dropColumn('program_text_color');
            $table->dropColumn('program_date_time_color');
            $table->dropColumn('program_icon_color');
            $table->dropColumn('program_description_color');
            $table->dropColumn('program_detail_background_color');
            $table->dropColumn('active_question_attendee_detail_color');
            $table->dropColumn('active_question_text_color');
            $table->dropColumn('active_question_speaker_heading_background_color');
            $table->dropColumn('active_question_speaker_heading_text_color');
            $table->dropColumn('active_question_speaker_listing_text_color');
            $table->dropColumn('active_question_icon_color');
            $table->dropColumn('active_question_like_count_color');
            $table->dropColumn('active_question_box_background_color');
            $table->dropColumn('question_attendee_detail_color');
            $table->dropColumn('question_text_color');
            $table->dropColumn('question_speaker_heading_background_color');
            $table->dropColumn('question_speaker_heading_text_color');
            $table->dropColumn('question_speaker_listing_text_color');
            $table->dropColumn('question_icon_color');
            $table->dropColumn('question_like_count_color');
            $table->dropColumn('question_box_background_color');
        });
        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('program_heading_text_color');
                $table->dropColumn('program_heading_background_color');
                $table->dropColumn('program_text_color');
                $table->dropColumn('program_date_time_color');
                $table->dropColumn('program_icon_color');
                $table->dropColumn('program_description_color');
                $table->dropColumn('program_detail_background_color');
                $table->dropColumn('active_question_attendee_detail_color');
                $table->dropColumn('active_question_text_color');
                $table->dropColumn('active_question_speaker_heading_background_color');
                $table->dropColumn('active_question_speaker_heading_text_color');
                $table->dropColumn('active_question_speaker_listing_text_color');
                $table->dropColumn('active_question_icon_color');
                $table->dropColumn('active_question_like_count_color');
                $table->dropColumn('active_question_box_background_color');
                $table->dropColumn('question_attendee_detail_color');
                $table->dropColumn('question_text_color');
                $table->dropColumn('question_speaker_heading_background_color');
                $table->dropColumn('question_speaker_heading_text_color');
                $table->dropColumn('question_speaker_listing_text_color');
                $table->dropColumn('question_icon_color');
                $table->dropColumn('question_like_count_color');
                $table->dropColumn('question_box_background_color');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
