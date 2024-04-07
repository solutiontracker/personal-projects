<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventAttendeesEmailHistoryTable extends Migration
    {

        const TABLE = 'conf_event_attendees_email_history';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->bigInteger('event_id');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->dateTime('email_date');
                $table->timestamps();
            $table->softDeletes();
                $table->index(['event_id', 'attendee_id'], 'event_id');
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->bigInteger('event_id');
                    $table->bigInteger('attendee_id')->index('attendee_id');
                    $table->dateTime('email_date');
                    $table->timestamps();
            $table->softDeletes();
                    $table->index(['event_id', 'attendee_id'], 'event_id');
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
