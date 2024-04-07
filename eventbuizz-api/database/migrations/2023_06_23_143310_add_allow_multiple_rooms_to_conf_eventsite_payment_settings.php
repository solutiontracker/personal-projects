<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllowMultipleRoomsToConfEventsitePaymentSettings extends Migration
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
            $table->tinyInteger('allow_single_room_only')->default(0);
            $table->tinyInteger('allow_multiple_bookings')->default(1);
            $table->tinyInteger('allow_one_person_to_one_room_only')->default(1);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('allow_single_room_only')->default(0);
                $table->tinyInteger('allow_multiple_bookings')->default(1);
                $table->tinyInteger('allow_one_person_to_one_room_only')->default(1);
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
            $table->dropColumn('allow_single_room_only');
            $table->dropColumn('allow_multiple_bookings');
            $table->dropColumn('allow_one_person_to_one_room_only');
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('allow_single_room_only');
                $table->dropColumn('allow_multiple_bookings');
                $table->dropColumn('allow_one_person_to_one_room_only');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
