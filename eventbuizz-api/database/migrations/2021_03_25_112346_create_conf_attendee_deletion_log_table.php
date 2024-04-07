<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfAttendeeDeletionLogTable extends Migration
    {
        const TABLE = 'conf_attendee_deletion_log';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->bigInteger('order_id')->index('order_id');
                $table->integer('additional_attendee');
                $table->timestamp('date')->useCurrent();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->bigInteger('event_id');
                    $table->bigInteger('attendee_id');
                    $table->bigInteger('order_id');
                    $table->integer('additional_attendee');
                    $table->timestamp('date')->useCurrent();
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
