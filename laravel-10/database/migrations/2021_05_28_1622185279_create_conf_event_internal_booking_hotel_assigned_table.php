<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfEventInternalBookingHotelAssignedTable extends Migration
{
    const TABLE = 'conf_event_internal_booking_hotel_assigned';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('hotel_id')->index('hotel_id')->nullable();
            $table->bigInteger('attendee_id')->index('attendee_id')->nullable();
            $table->string('name',255)->nullable();
            $table->double('price',8, 2)->nullable();
            $table->enum('price_type', ['fixed', 'notfixed']);
            $table->integer('rooms')->nullable();
            $table->date('checkin');
            $table->date('checkout');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('hotel_id')->index('hotel_id')->nullable();
                $table->bigInteger('attendee_id')->index('attendee_id')->nullable();
                $table->string('name',255)->nullable();
                $table->double('price',8, 2)->nullable();
                $table->enum('price_type', ['fixed', 'notfixed']);
                $table->integer('rooms')->nullable();
                $table->date('checkin');
                $table->date('checkout');
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
