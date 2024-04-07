<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfReservationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_reservation_settings';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->bigInteger('event_id')->index('event_id');
            $table->bigInteger('entity_id')->index('entity_id');
            $table->enum('entity_type', ['S', 'E'])->nullable();
            $table->bigInteger('contact_id')->index('contact_id');
            $table->tinyInteger('auto_save')->default(0);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('entity_id')->index('entity_id');
                $table->enum('entity_type', ['S', 'E'])->nullable();
                $table->bigInteger('contact_id')->index('contact_id');
                $table->tinyInteger('auto_save')->default(0);
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