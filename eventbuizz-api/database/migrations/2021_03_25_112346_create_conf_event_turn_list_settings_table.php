<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventTurnListSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_turn_list_settings';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('event_id')->index('event_id');
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('turnlist_attendee_approval')->default(0);
            $table->tinyInteger('enable_speech_time')->default(1);
            $table->tinyInteger('enable_speech_time_for_moderator')->default(0);
            $table->tinyInteger('display_time')->default(1);
            $table->tinyInteger('show_image_turnlist')->default(1);
            $table->tinyInteger('show_company_turnlist')->default(1);
            $table->tinyInteger('show_title_turnlist')->default(1);
            $table->tinyInteger('show_awaiting_turnlist')->default(1);
            $table->tinyInteger('show_delegate_turnlist')->default(1);
            $table->tinyInteger('show_department_turnlist')->default(1);
            $table->tinyInteger('show_program_section')->default(1);
            $table->tinyInteger('show_network_group_turnlist')->nullable()->default(0);
            $table->integer('speak_time')->default(300);
            $table->bigInteger('turn_project_refresh_time')->default(5);
            $table->string('delegate_label')->default('Delegate');
            $table->string('network_label')->nullable()->default('Network Group');
            $table->text('lobby_url')->nullable();
            $table->tinyInteger('show_dashboard')->default(1);
            $table->enum('streaming_option', ['agora', 'kinesis'])->nullable()->default('agora');
            $table->string('program_heading_background_color')->default('#262626');
            $table->string('program_heading_text_color')->default('#FFFFFF');
            $table->string('program_text_color')->default('#262626');
            $table->string('program_date_time_color')->default('#262626');
            $table->string('program_icon_color')->default('#262626');
            $table->string('program_description_color')->default('#262626');
            $table->text('lobby_name')->nullable();
            $table->string('department_label')->default('Department');
            $table->bigInteger('time_between_attendees');
            $table->string('background_image');
            $table->string('background_color');
            $table->string('headings_color')->nullable();
            $table->string('text_color')->default('#262626');
            $table->string('description_color')->nullable();
            $table->string('program_section_color')->nullable();
            $table->float('font_size', 10, 0)->nullable();
            $table->string('text_color1')->default('#000000');
            $table->string('text_color2')->default('#000000');
            $table->string('text_color3')->default('#000000');
            $table->tinyInteger('organizer_info')->default(0);
            $table->tinyInteger('ask_to_apeak')->default(1);
            $table->tinyInteger('ask_to_speak_notes')->default(1);
            $table->text('av_output_all_template')->nullable();
            $table->text('av_output_active_template')->nullable();
            $table->text('av_output_sub_active_template')->nullable();
            $table->text('av_output_next_template')->nullable();
            $table->text('av_output_count_template')->nullable();
            $table->string('active_bg_color')->nullable();
            $table->string('all_bg_color')->nullable();
            $table->string('count_bg_color')->nullable();
            $table->string('live_attendee_detail_bg_color')->nullable()->default('#FFFFFF');
            $table->string('speaking_now_background_color')->nullable()->default('#F28121');
            $table->string('speaking_now_text_color')->nullable()->default('#FFFFFF');
            $table->string('speaker_text_color')->nullable()->default('#262626');
            $table->string('attendee_detail_background_color')->nullable()->default('#FFFFFF');
            $table->string('program_detail_background_color')->nullable()->default('#FFFFFF');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('event_id')->index('event_id');
                $table->tinyInteger('status')->default(0);
                $table->tinyInteger('turnlist_attendee_approval')->default(0);
                $table->tinyInteger('enable_speech_time')->default(1);
                $table->tinyInteger('enable_speech_time_for_moderator')->default(0);
                $table->tinyInteger('display_time')->default(1);
                $table->tinyInteger('show_image_turnlist')->default(1);
                $table->tinyInteger('show_company_turnlist')->default(1);
                $table->tinyInteger('show_title_turnlist')->default(1);
                $table->tinyInteger('show_awaiting_turnlist')->default(1);
                $table->tinyInteger('show_delegate_turnlist')->default(1);
                $table->tinyInteger('show_department_turnlist')->default(1);
                $table->tinyInteger('show_program_section')->default(1);
                $table->tinyInteger('show_network_group_turnlist')->nullable()->default(0);
                $table->integer('speak_time')->default(300);
                $table->bigInteger('turn_project_refresh_time')->default(5);
                $table->string('delegate_label')->default('Delegate');
                $table->string('network_label')->nullable()->default('Network Group');
                $table->text('lobby_url')->nullable();
                $table->tinyInteger('show_dashboard')->default(1);
                $table->enum('streaming_option', ['agora', 'kinesis'])->nullable()->default('agora');
                $table->string('program_heading_background_color')->default('#262626');
                $table->string('program_heading_text_color')->default('#FFFFFF');
                $table->string('program_text_color')->default('#262626');
                $table->string('program_date_time_color')->default('#262626');
                $table->string('program_icon_color')->default('#262626');
                $table->string('program_description_color')->default('#262626');
                $table->text('lobby_name')->nullable();
                $table->string('department_label')->default('Department');
                $table->bigInteger('time_between_attendees');
                $table->string('background_image');
                $table->string('background_color');
                $table->string('headings_color')->nullable();
                $table->string('text_color')->default('#262626');
                $table->string('description_color')->nullable();
                $table->string('program_section_color')->nullable();
                $table->float('font_size', 10, 0)->nullable();
                $table->string('text_color1')->default('#000000');
                $table->string('text_color2')->default('#000000');
                $table->string('text_color3')->default('#000000');
                $table->tinyInteger('organizer_info')->default(0);
                $table->tinyInteger('ask_to_apeak')->default(1);
                $table->tinyInteger('ask_to_speak_notes')->default(1);
                $table->text('av_output_all_template')->nullable();
                $table->text('av_output_active_template')->nullable();
                $table->text('av_output_sub_active_template')->nullable();
                $table->text('av_output_next_template')->nullable();
                $table->text('av_output_count_template')->nullable();
                $table->string('active_bg_color')->nullable();
                $table->string('all_bg_color')->nullable();
                $table->string('count_bg_color')->nullable();
                $table->string('live_attendee_detail_bg_color')->nullable()->default('#FFFFFF');
                $table->string('speaking_now_background_color')->nullable()->default('#F28121');
                $table->string('speaking_now_text_color')->nullable()->default('#FFFFFF');
                $table->string('speaker_text_color')->nullable()->default('#262626');
                $table->string('attendee_detail_background_color')->nullable()->default('#FFFFFF');
                $table->string('program_detail_background_color')->nullable()->default('#FFFFFF');
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
