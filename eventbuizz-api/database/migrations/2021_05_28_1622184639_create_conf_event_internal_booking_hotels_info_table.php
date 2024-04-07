<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfEventInternalBookingHotelsInfoTable extends Migration
{
    const TABLE = 'conf_event_internal_booking_hotels_info';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('hotel_id')->index('hotel_id')->nullable();
            $table->string('name',255)->nullable();
            $table->text('value');
            $table->bigInteger('languages_id')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('hotel_id')->index('hotel_id')->nullable();
                $table->string('name',255)->nullable();
                $table->text('value');
                $table->bigInteger('languages_id')->nullable();
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
