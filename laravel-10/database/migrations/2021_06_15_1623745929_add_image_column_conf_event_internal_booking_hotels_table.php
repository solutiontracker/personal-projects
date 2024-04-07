<?php
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageColumnConfEventInternalBookingHotelsTable extends Migration
{
    const TABLE = 'conf_event_internal_booking_hotels';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('image')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('image')->nullable();
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('image');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('image');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
