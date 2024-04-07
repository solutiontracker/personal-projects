<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventAlertAttendeesTable extends Migration
    {
        const TABLE = 'conf_event_alert_attendees';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->dateTime('date');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->bigInteger('alert_id')->index('alert_id');
                $table->tinyInteger('status')->index('status')->comment('1==Not Read, 2 ==Read ');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->dateTime('date');
                    $table->bigInteger('attendee_id')->index('attendee_id');
                    $table->bigInteger('alert_id')->index('alert_id');
                    $table->tinyInteger('status')->index('status')->comment('1==Not Read, 2 ==Read ');
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
