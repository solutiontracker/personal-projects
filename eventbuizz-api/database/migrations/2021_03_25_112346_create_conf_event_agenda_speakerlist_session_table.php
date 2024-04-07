<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventAgendaSpeakerlistSessionTable extends Migration
    {
        const TABLE = 'conf_event_agenda_speakerlist_session';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('event_id')->index('event_id');
                $table->integer('agenda_id')->index('agenda_id');
                $table->tinyInteger('is_active')->default(0)->index('is_active');
                $table->dateTime('session_date')->index('session_date');
                $table->time('start_time');
                $table->time('end_time');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('event_id')->index('event_id');
                    $table->integer('agenda_id')->index('agenda_id');
                    $table->tinyInteger('is_active')->default(0)->index('is_active');
                    $table->dateTime('session_date')->index('session_date');
                    $table->time('start_time');
                    $table->time('end_time');
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
