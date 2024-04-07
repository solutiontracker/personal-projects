<?php
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttendeeCvColumnConfAttendeesTable extends Migration
{
    const TABLE = 'conf_attendees';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('attendee_cv')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('attendee_cv')->nullable();
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('attendee_cv');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('attendee_cv');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
