<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventAttendeePollAuthorityLogTable extends Migration
    {
        const TABLE = 'conf_event_attendee_poll_authority_log';

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
                $table->bigInteger('attendee_to')->index('attendee_to');
                $table->bigInteger('attendee_from')->index('attendee_from');
                $table->tinyInteger('is_accepted')->default(0)->index('is_accepted');
                $table->tinyInteger('is_read_to')->default(0);
                $table->tinyInteger('is_read_from')->default(0);
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->bigInteger('attendee_to')->index('attendee_to');
                    $table->bigInteger('attendee_from')->index('attendee_from');
                    $table->tinyInteger('is_accepted')->default(0)->index('is_accepted');
                    $table->tinyInteger('is_read_to')->default(0);
                    $table->tinyInteger('is_read_from')->default(0);
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
