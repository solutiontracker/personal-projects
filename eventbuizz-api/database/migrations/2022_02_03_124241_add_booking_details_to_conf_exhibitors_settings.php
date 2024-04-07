<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBookingDetailsToConfExhibitorsSettings extends Migration
{
    const TABLE = 'conf_exhibitors_settings';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('booking_details')->default(1);
            $table->tinyInteger('reservation_display_filters')->default(0);
            $table->tinyInteger('reservation_time_slots')->default(0);
            $table->tinyInteger('reservation_available_meeting_rooms')->default(0);
            $table->tinyInteger('reservation_meeting_rooms')->default(0);
            $table->tinyInteger('reservation_display_colleagues')->default(0);
            $table->tinyInteger('reservation_display_company')->default(0);
            $table->tinyInteger('colleague_book_meeting')->default(0);
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('booking_details')->default(1);
                $table->tinyInteger('reservation_display_filters')->default(0);
                $table->tinyInteger('reservation_time_slots')->default(0);
                $table->tinyInteger('reservation_available_meeting_rooms')->default(0);
                $table->tinyInteger('reservation_meeting_rooms')->default(0);
                $table->tinyInteger('reservation_display_colleagues')->default(0);
                $table->tinyInteger('reservation_display_company')->default(0);
                $table->tinyInteger('colleague_book_meeting')->default(0);
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('booking_details');
            $table->dropColumn('reservation_display_filters');
            $table->dropColumn('reservation_time_slots');
            $table->dropColumn('reservation_available_meeting_rooms');
            $table->dropColumn('reservation_meeting_rooms');
            $table->dropColumn('reservation_display_colleagues');
            $table->dropColumn('reservation_display_company');
            $table->dropColumn('colleague_book_meeting');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('booking_details');
                $table->dropColumn('reservation_display_filters');
                $table->dropColumn('reservation_time_slots');
                $table->dropColumn('reservation_available_meeting_rooms');
                $table->dropColumn('reservation_meeting_rooms');
                $table->dropColumn('reservation_display_colleagues');
                $table->dropColumn('reservation_display_company');
                $table->dropColumn('colleague_book_meeting');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
