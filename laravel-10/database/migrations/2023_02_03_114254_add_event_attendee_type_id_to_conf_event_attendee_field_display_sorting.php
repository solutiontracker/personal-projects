<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventAttendeeTypeIdToConfEventAttendeeFieldDisplaySorting extends Migration
{
    const TABLE = 'conf_event_attendee_field_display_sorting';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->unsignedBigInteger('event_attendee_type_id')->default(0)->index('event_attendee_type_id');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->unsignedBigInteger('event_attendee_type_id')->default(0)->index('event_attendee_type_id');
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
            $table->dropColumn('event_attendee_type_id');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('event_attendee_type_id');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}