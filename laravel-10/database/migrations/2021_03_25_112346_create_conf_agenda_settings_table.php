<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfAgendaSettingsTable extends Migration
    {
        const TABLE = 'conf_agenda_settings';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->bigInteger('event_id')->index('event_id');
                $table->tinyInteger('agenda_list')->default(0)->index('agenda_list')->comment('0=time,1=track');
                $table->tinyInteger('session_ratings')->default(1)->index('session_ratings');
                $table->tinyInteger('agenda_tab')->default(0)->index('agenda_tab');
                $table->tinyInteger('admin_fav_attendee')->default(1)->index('admin_fav_attendee');
                $table->tinyInteger('attach_attendee_mobile')->default(0)->index('attach_attendee_mobile');
                $table->tinyInteger('qa')->default(1);
                $table->tinyInteger('program_fav')->default(0);
                $table->tinyInteger('show_tracks')->default(1);
                $table->tinyInteger('show_attach_attendee')->default(1);
                $table->tinyInteger('agenda_display_time')->default(1);
                $table->tinyInteger('show_program_dashboard')->default(1);
                $table->tinyInteger('show_my_program_dashboard')->default(0);
                $table->tinyInteger('agenda_collapse_workshop')->default(0);
                $table->integer('agendaTimer');
                $table->tinyInteger('agenda_search_filter')->default(0);
                $table->tinyInteger('agenda_display_alerts')->default(0);
                $table->tinyInteger('enable_notes')->default(1);
                $table->tinyInteger('enable_program_attendee')->default(0);
                $table->tinyInteger('program_groups')->default(1);
                $table->timestamps();
            $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->bigInteger('event_id');
                    $table->tinyInteger('agenda_list')->default(0)->comment('0=time,1=track');
                    $table->tinyInteger('session_ratings')->default(1);
                    $table->tinyInteger('agenda_tab')->default(0);
                    $table->tinyInteger('admin_fav_attendee')->default(1);
                    $table->tinyInteger('attach_attendee_mobile')->default(0);
                    $table->tinyInteger('qa')->default(1);
                    $table->tinyInteger('program_fav')->default(0);
                    $table->tinyInteger('show_tracks')->default(1);
                    $table->tinyInteger('show_attach_attendee')->default(1);
                    $table->tinyInteger('agenda_display_time')->default(1);
                    $table->tinyInteger('show_program_dashboard')->default(1);
                    $table->tinyInteger('show_my_program_dashboard')->default(0);
                    $table->tinyInteger('agenda_collapse_workshop')->default(0);
                    $table->integer('agendaTimer');
                    $table->tinyInteger('agenda_search_filter')->default(0);
                    $table->tinyInteger('agenda_display_alerts')->default(0);
                    $table->tinyInteger('enable_notes')->default(1);
                    $table->tinyInteger('enable_program_attendee')->default(0);
                    $table->tinyInteger('program_groups')->default(1);
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
