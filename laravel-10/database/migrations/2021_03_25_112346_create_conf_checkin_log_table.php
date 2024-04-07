<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfCheckinLogTable extends Migration
    {
        const TABLE = 'conf_checkin_log';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->dateTime('checkin');
                $table->dateTime('checkout');
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('organizer_id')->index('organizer_id');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->integer('admin_id')->index('admin_id');
                $table->enum('type_name', ['event', 'program', 'group', 'ticket'])->default('event');
                $table->bigInteger('type_id');
                $table->text('data');
                $table->tinyInteger('status')->index('status');
                $table->string('delegate', 1000)->nullable();
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->dateTime('checkin');
                    $table->dateTime('checkout');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->bigInteger('organizer_id')->index('organizer_id');
                    $table->bigInteger('attendee_id')->index('attendee_id');
                    $table->integer('admin_id')->index('admin_id');
                    $table->enum('type_name', ['event', 'program', 'group', 'ticket'])->default('event');
                    $table->bigInteger('type_id');
                    $table->text('data');
                    $table->tinyInteger('status')->index('status');
                    $table->string('delegate', 1000)->nullable();
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
