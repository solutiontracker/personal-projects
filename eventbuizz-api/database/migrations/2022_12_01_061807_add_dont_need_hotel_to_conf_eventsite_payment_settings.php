<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDontNeedHotelToConfEventsitePaymentSettings extends Migration
{
    const TABLE = 'conf_eventsite_payment_settings';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('dont_need_hotel')->default(0);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('dont_need_hotel')->default(0);
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
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('dont_need_hotel');
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('dont_need_hotel');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}