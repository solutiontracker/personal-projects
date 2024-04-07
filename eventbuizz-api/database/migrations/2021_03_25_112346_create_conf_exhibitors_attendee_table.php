<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfExhibitorsAttendeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_exhibitors_attendee';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('exhibitor_id')->index('exhibitor_id');
            $table->bigInteger('attendee_id')->index('attendee_id');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('exhibitor_id')->index('exhibitor_id');
                $table->bigInteger('attendee_id')->index('attendee_id');
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