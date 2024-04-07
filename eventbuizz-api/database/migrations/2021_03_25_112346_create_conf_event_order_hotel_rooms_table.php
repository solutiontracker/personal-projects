<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventOrderHotelRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_order_hotel_rooms';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->integer('order_hotel_id');
            $table->integer('hotel_id');
            $table->integer('room_id');
            $table->integer('order_id');
            $table->integer('event_id');
            $table->date('reserve_date');
            $table->integer('rooms');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->integer('order_hotel_id');
                $table->integer('hotel_id');
                $table->integer('room_id');
                $table->integer('order_id');
                $table->integer('event_id');
                $table->date('reserve_date');
                $table->integer('rooms');
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
