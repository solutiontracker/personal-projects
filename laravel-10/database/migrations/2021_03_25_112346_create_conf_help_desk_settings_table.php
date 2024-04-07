<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfHelpDeskSettingsTable extends Migration
    {
        const TABLE = 'conf_help_desk_settings';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->tinyInteger('countdown_time');
                $table->tinyInteger('parallel_session_projector')->default(0);
                $table->integer('project_list_time')->default(10);
                $table->integer('max_project_list_time')->default(0);
                $table->bigInteger('event_id')->index('event_id');
                $table->tinyInteger('help_desk_answers_view')->default(1);
                $table->tinyInteger('send_attendee_email')->default(0);
                $table->tinyInteger('show_attendee_popup')->default(0);
                $table->tinyInteger('moderator')->default(1);
                $table->tinyInteger('projector_program')->default(1);
                $table->tinyInteger('organizer_info')->default(1);
                $table->tinyInteger('archive')->default(1);
                $table->tinyInteger('up_vote')->default(1);
                $table->tinyInteger('help_desk_listing')->default(1);
                $table->tinyInteger('help_desk_tabs')->default(1);
                $table->tinyInteger('anonymous')->default(1);
                $table->tinyInteger('order_by_likes')->default(0);
                $table->string('background_color');
                $table->string('headings_color');
                $table->string('description_color');
                $table->string('program_section_color');
                $table->float('font_size', 10, 0);
                $table->tinyInteger('show_profile_images')->default(1);
                $table->timestamps();
                $table->softDeletes();
                $table->tinyInteger('qa_answers_view')->default(1);
                $table->tinyInteger('qa_listing')->default(1);
                $table->tinyInteger('qa_tabs')->default(1);
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->tinyInteger('countdown_time');
                    $table->tinyInteger('parallel_session_projector')->default(0);
                    $table->integer('project_list_time')->default(10);
                    $table->integer('max_project_list_time')->default(0);
                    $table->bigInteger('event_id')->index('event_id');
                    $table->tinyInteger('help_desk_answers_view')->default(1);
                    $table->tinyInteger('send_attendee_email')->default(0);
                    $table->tinyInteger('show_attendee_popup')->default(0);
                    $table->tinyInteger('moderator')->default(1);
                    $table->tinyInteger('projector_program')->default(1);
                    $table->tinyInteger('organizer_info')->default(1);
                    $table->tinyInteger('archive')->default(1);
                    $table->tinyInteger('up_vote')->default(1);
                    $table->tinyInteger('help_desk_listing')->default(1);
                    $table->tinyInteger('help_desk_tabs')->default(1);
                    $table->tinyInteger('anonymous')->default(1);
                    $table->tinyInteger('order_by_likes')->default(0);
                    $table->string('background_color');
                    $table->string('headings_color');
                    $table->string('description_color');
                    $table->string('program_section_color');
                    $table->float('font_size', 10, 0);
                    $table->tinyInteger('show_profile_images')->default(1);
                    $table->timestamps();
                    $table->softDeletes();
                    $table->tinyInteger('qa_answers_view')->default(1);
                    $table->tinyInteger('qa_listing')->default(1);
                    $table->tinyInteger('qa_tabs')->default(1);
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
