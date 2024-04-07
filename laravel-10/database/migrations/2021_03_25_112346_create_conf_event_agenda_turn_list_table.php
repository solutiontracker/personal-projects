<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventAgendaTurnListTable extends Migration
    {
        const TABLE = 'conf_event_agenda_turn_list';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->string('status', 45)->index('status');
                $table->integer('sort_order');
                $table->bigInteger('agenda_id')->index('agenda_id');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->dateTime('speech_start_time');
                $table->dateTime('moderator_speech_start_time')->nullable();
                $table->dateTime('moderator_speech_end_time')->nullable();
                $table->text('notes');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->string('status', 45)->index('status');
                    $table->integer('sort_order');
                    $table->bigInteger('agenda_id')->index('agenda_id');
                    $table->bigInteger('attendee_id')->index('attendee_id');
                    $table->dateTime('speech_start_time');
                    $table->dateTime('moderator_speech_start_time')->nullable();
                    $table->dateTime('moderator_speech_end_time')->nullable();
                    $table->text('notes');
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
