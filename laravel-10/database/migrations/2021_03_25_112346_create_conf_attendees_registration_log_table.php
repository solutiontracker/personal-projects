<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfAttendeesRegistrationLogTable extends Migration
    {
        const TABLE = 'conf_attendees_registration_log';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->bigInteger('organizer_id')->index('organizer_id');
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->dateTime('reg_date');
                $table->dateTime('cancel_date');
                $table->enum('status', ['active', 'cancel'])->default('active')->index('status');
                $table->text('comments')->nullable();
                $table->enum('register_by', ['admin', 'front', 'autoregister'])->default('front')->index('register_by');
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->bigInteger('organizer_id')->index('organizer_id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->bigInteger('attendee_id')->index('attendee_id');
                    $table->dateTime('reg_date');
                    $table->dateTime('cancel_date');
                    $table->enum('status', ['active', 'cancel'])->default('active')->index('status');
                    $table->text('comments')->nullable();
                    $table->enum('register_by', ['admin', 'front', 'autoregister'])->default('front')->index('register_by');
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
