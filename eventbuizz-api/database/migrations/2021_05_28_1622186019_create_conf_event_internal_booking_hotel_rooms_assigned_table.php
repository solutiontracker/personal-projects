<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfEventInternalBookingHotelRoomsAssignedTable extends Migration
{
    const TABLE = 'conf_event_internal_booking_hotel_rooms_assigned';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('assign_hotel_id')->index('assign_hotel_id')->nullable();
            $table->bigInteger('hotel_id')->index('hotel_id')->nullable();
            $table->bigInteger('room_id')->index('room_id')->nullable();
            $table->bigInteger('attendee_id')->index('attendee_id')->nullable();
            $table->bigInteger('event_id')->index('event_id')->nullable();
            $table->integer('rooms')->nullable();
            $table->date('reserve_date');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('assign_hotel_id')->index('assign_hotel_id')->nullable();
                $table->bigInteger('hotel_id')->index('hotel_id')->nullable();
                $table->bigInteger('room_id')->index('room_id')->nullable();
                $table->bigInteger('attendee_id')->index('attendee_id')->nullable();
                $table->bigInteger('event_id')->index('event_id')->nullable();
                $table->integer('rooms')->nullable();
                $table->date('reserve_date');
                $table->timestamps();
                $table->softDeletes();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        EBSchema::dropDeleteTrigger(self::TABLE);
        Schema::dropIfExists(self::TABLE);
        Schema::connection(config('database.archive_connection'))->dropIfExists(self::TABLE);
    }
}
