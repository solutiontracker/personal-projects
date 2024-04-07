<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventHotelsPersonsTable extends Migration
    {
        const TABLE = 'conf_event_hotels_persons';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->bigInteger('order_hotel_id')->nullable();
                $table->bigInteger('order_id')->index('Order Id');
                $table->bigInteger('hotel_id')->index('Hotel Id');
                $table->string('name')->nullable();
                $table->integer('dob')->nullable();
                $table->integer('room_no')->nullable();
                $table->integer('attendee_id')->nullable()->default(0);
                $table->timestamps();
                $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->bigInteger('order_hotel_id')->nullable();
                    $table->bigInteger('order_id')->index('Order Id');
                    $table->bigInteger('hotel_id')->index('Hotel Id');
                    $table->string('name')->nullable();
                    $table->integer('dob')->nullable();
                    $table->integer('room_no')->nullable();
                    $table->integer('attendee_id')->nullable()->default(0);
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
