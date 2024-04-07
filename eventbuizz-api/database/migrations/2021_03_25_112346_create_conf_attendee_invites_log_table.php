<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfAttendeeInvitesLogTable extends Migration
    {
        const TABLE = 'conf_attendee_invites_log';

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
                $table->string('email', 250);
                $table->string('phone', 55)->nullable();
                $table->tinyInteger('email_sent');
                $table->tinyInteger('sms_sent')->default(0);
                $table->tinyInteger('not_sent')->default(0);
                $table->dateTime('date_sent');
                $table->enum('type', ['invite', 'not_registered']);
                $table->tinyInteger('status');
                $table->text('status_msg');
                $table->text('sms');
                $table->timestamps();
            $table->softDeletes();
                $table->index(['event_id', 'organizer_id'], 'event_id');
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->bigInteger('event_id');
                    $table->bigInteger('organizer_id')->index('organizer_id');
                    $table->string('first_name', 250);
                    $table->string('last_name', 250);
                    $table->string('email', 250);
                    $table->string('phone', 55)->nullable();
                    $table->tinyInteger('email_sent');
                    $table->tinyInteger('sms_sent')->default(0);
                    $table->tinyInteger('not_sent')->default(0);
                    $table->dateTime('date_sent');
                    $table->enum('type', ['invite', 'not_registered']);
                    $table->tinyInteger('status');
                    $table->text('status_msg');
                    $table->text('sms');
                    $table->timestamps();
            $table->softDeletes();
                    $table->index(['event_id', 'organizer_id'], 'event_id');
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
