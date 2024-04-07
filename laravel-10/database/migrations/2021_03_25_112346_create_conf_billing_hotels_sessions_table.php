<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfBillingHotelsSessionsTable extends Migration
    {
        const TABLE = 'conf_billing_hotels_sessions';

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
                $table->bigInteger('hotel_id')->index('hotel_id');
                $table->integer('rooms');
                $table->integer('room_id');
                $table->dateTime('date_reserved');
                $table->string('session_id')->index('session_id');
                $table->tinyInteger('is_release')->default(1);
                $table->tinyInteger('status')->index('status');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->bigInteger('hotel_id')->index('hotel_id');
                    $table->integer('rooms');
                    $table->integer('room_id');
                    $table->dateTime('date_reserved');
                    $table->string('session_id')->index('session_id');
                    $table->tinyInteger('is_release')->default(1);
                    $table->tinyInteger('status')->index('status');
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
