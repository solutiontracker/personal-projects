<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_notifications';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('event_id')->index('event_id');
            $table->bigInteger('attendee_id')->index('attendee_id');
            $table->string('type', 250);
            $table->string('link', 250);
            $table->tinyInteger('status')->index('status');
            $table->dateTime('date');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->string('type', 250);
                $table->string('link', 250);
                $table->tinyInteger('status')->index('status');
                $table->dateTime('date');
                $table->timestamps();
            $table->softDeletes();
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
        EBSchema::dropDeleteTrigger(self::TABLE);
        Schema::dropIfExists(self::TABLE);
            Schema::connection(config('database.archive_connection'))->dropIfExists(self::TABLE);
    }
}
