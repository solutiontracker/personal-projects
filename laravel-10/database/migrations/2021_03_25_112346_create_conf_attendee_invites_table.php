<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfAttendeeInvitesTable extends Migration
    {
        const TABLE = 'conf_attendee_invites';

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
                $table->bigInteger('organizer_id')->index('organizer_id');
                $table->string('first_name', 250);
                $table->string('last_name', 250);
                $table->string('email', 250)->index('email');
                $table->string('phone', 55);
                $table->tinyInteger('status')->comment('0=not-sent,1=sent,2=reminder-sent');
                $table->tinyInteger('sms_sent')->default(0)->index('sms_sent');
                $table->tinyInteger('not_send')->default(0)->index('not_send');
                $table->tinyInteger('is_attending')->default(0)->index('is_attending');
                $table->tinyInteger('is_resend')->default(0)->index('is_resend');
                $table->dateTime('date_sent')->index('date_sent');
                $table->string('ss_number')->nullable();
                $table->tinyInteger('allow_vote')->default(0);
                $table->tinyInteger('ask_to_speak')->default(0);
                $table->timestamps();
            $table->softDeletes();
                $table->index(['event_id', 'organizer_id', 'status'], 'event_id');
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->bigInteger('event_id');
                    $table->bigInteger('organizer_id')->index('organizer_id');
                    $table->string('first_name', 250);
                    $table->string('last_name', 250);
                    $table->string('email', 250)->index('email');
                    $table->string('phone', 55);
                    $table->tinyInteger('status')->comment('0=not-sent,1=sent,2=reminder-sent');
                    $table->tinyInteger('sms_sent')->default(0)->index('sms_sent');
                    $table->tinyInteger('not_send')->default(0)->index('not_send');
                    $table->tinyInteger('is_attending')->default(0)->index('is_attending');
                    $table->tinyInteger('is_resend')->default(0)->index('is_resend');
                    $table->dateTime('date_sent')->index('date_sent');
                    $table->string('ss_number')->nullable();
                    $table->tinyInteger('allow_vote')->default(0);
                    $table->tinyInteger('ask_to_speak')->default(0);
                    $table->timestamps();
            $table->softDeletes();
                    $table->index(['event_id', 'organizer_id', 'status'], 'event_id');
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
