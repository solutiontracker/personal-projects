<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateTableConfAttendeeCprChangeLogs extends Migration
{
    const TABLE = 'conf_attendee_cpr_change_logs';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->index('event_id');
            $table->unsignedBigInteger('changed_by_organizer_id')->index('changed_by_organizer_id');
            $table->unsignedBigInteger('model_id')->index('model_id');
            $table->string('model_name');
            $table->unsignedBigInteger('new_attendee_id')->index('new_attendee_id');
            $table->string('ss_number');
            $table->enum('updated_from',['API','IMPORT']);
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->unsignedBigInteger('event_id')->index('event_id');
                $table->unsignedBigInteger('changed_by_organizer_id')->index('changed_by_organizer_id');
                $table->unsignedBigInteger('model_id')->index('model_id');
                $table->string('model_name');
                $table->unsignedBigInteger('new_attendee_id')->index('new_attendee_id');
                $table->string('ss_number');
                $table->enum('updated_from',['API','IMPORT']);
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
