<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventAttendeesAppInviteLogTable extends Migration
    {
        const TABLE = 'conf_event_attendees_app_invite_log';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->tinyInteger('email_sent')->default(0)->index('email_sent');
                $table->tinyInteger('sms_sent')->default(0)->index('sms_sent');
                $table->dateTime('email_date');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->bigInteger('attendee_id')->index('attendee_id');
                    $table->tinyInteger('email_sent')->default(0)->index('email_sent');
                    $table->tinyInteger('sms_sent')->default(0)->index('sms_sent');
                    $table->dateTime('email_date');
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
