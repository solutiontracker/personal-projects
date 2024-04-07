<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShowSummaryAndCheckinCheckoutToConfEventInternalBookingSettings extends Migration
{
    const TABLE = 'conf_event_internal_booking_settings';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('enable_checkin_checkout')->default(1);
            $table->tinyInteger('enable_show_summary')->default(1);

        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('enable_checkin_checkout')->default(1);
                $table->tinyInteger('enable_show_summary')->default(1);
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('enable_checkin_checkout');
            $table->tinyInteger('enable_show_summary');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('enable_checkin_checkout');
                $table->tinyInteger('enable_show_summary');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
