<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfEventInternalBookingHotelsTable extends Migration
{
    const TABLE = 'conf_event_internal_booking_hotels';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('event_id')->index('event_id')->nullable();
            $table->integer('rooms')->nullable();
            $table->string('name',255)->nullable();
            $table->double('price',8, 2)->nullable();
            $table->enum('price_type', ['fixed', 'notfixed']);
            $table->date('hotel_from_date');
            $table->date('hotel_to_date');
            $table->tinyInteger('sort_order')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('event_id')->index('event_id')->nullable();
                $table->integer('rooms')->nullable();
                $table->string('name',255)->nullable();
                $table->double('price',8, 2)->nullable();
                $table->enum('price_type', ['fixed', 'notfixed']);
                $table->date('hotel_from_date');
                $table->date('hotel_to_date');
                $table->tinyInteger('sort_order')->nullable();
                $table->tinyInteger('status')->nullable();
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
