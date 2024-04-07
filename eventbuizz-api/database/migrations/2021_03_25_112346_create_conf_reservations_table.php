<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfReservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_reservations';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->date('date')->nullable();
            $table->time('time_from')->nullable();
            $table->time('time_to')->nullable();
            $table->integer('duration')->nullable();
            $table->bigInteger('entity_id')->nullable()->index('entity_id');
            $table->enum('entity_type', ['S', 'E'])->nullable();
            $table->bigInteger('organizer_id')->nullable()->index('organizer_id');
            $table->bigInteger('event_id')->nullable()->index('event_id');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->date('date')->nullable();
                $table->time('time_from')->nullable();
                $table->time('time_to')->nullable();
                $table->integer('duration')->nullable();
                $table->bigInteger('entity_id')->nullable()->index('entity_id');
                $table->enum('entity_type', ['S', 'E'])->nullable();
                $table->bigInteger('organizer_id')->nullable()->index('organizer_id');
                $table->bigInteger('event_id')->nullable()->index('event_id');
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
