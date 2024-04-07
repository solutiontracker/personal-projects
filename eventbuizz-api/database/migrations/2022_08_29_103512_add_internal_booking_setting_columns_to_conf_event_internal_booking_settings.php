<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInternalBookingSettingColumnsToConfEventInternalBookingSettings extends Migration
{
    const TABLE = 'conf_event_internal_booking_settings';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('show_internal_booking_on_web_app')->default(0);
            $table->tinyInteger('show_internal_booking_on_native_app')->default(0);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('show_internal_booking_on_web_app')->default(0);
                $table->tinyInteger('show_internal_booking_on_native_app')->default(0);
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
            $table->dropColumn('show_internal_booking_on_web_app');
            $table->dropColumn('show_internal_booking_on_native_app');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('show_internal_booking_on_web_app');
                $table->dropColumn('show_internal_booking_on_native_app');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}