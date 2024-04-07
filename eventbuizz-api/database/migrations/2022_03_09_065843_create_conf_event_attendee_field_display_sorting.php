<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfEventAttendeeFieldDisplaySorting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_attendee_field_display_sorting';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id')->index('event_id');
            $table->string('fields_name');
            $table->integer('sort_order')->default(0)->index('sort_order');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('event_id')->index('event_id');
                $table->string('fields_name');
                $table->integer('sort_order')->default(0)->index('sort_order');
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
