<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHotelQtyToConfEventsitePaymentSettings extends Migration
{

    const TABLE = 'conf_eventsite_payment_settings';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->integer('hotel_qty')->default(20);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->integer('hotel_qty')->default(20);
            });
            
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->integer('hotel_qty');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->integer('hotel_qty');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

}
