<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfReservationSlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_reservation_slots';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->bigInteger('master_id');
            $table->date('date');
            $table->time('time_from');
            $table->time('time_to');
            $table->integer('duration');
            $table->bigInteger('entity_id')->index('entity_id');
            $table->enum('entity_type', ['S', 'E']);
            $table->bigInteger('organizer_id')->index('organizer_id');
            $table->bigInteger('event_id')->index('event_id');
            $table->bigInteger('contact_id')->index('contact_id');
            $table->bigInteger('reserved_by')->default(0);
            $table->text('notes');
            $table->string('company_name');
            $table->dateTime('reserved_date');
            $table->enum('status', ['Available', 'Pending', 'Booked'])->default('Available');
            $table->timestamps();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->bigInteger('master_id');
                $table->date('date');
                $table->time('time_from');
                $table->time('time_to');
                $table->integer('duration');
                $table->bigInteger('entity_id')->index('entity_id');
                $table->enum('entity_type', ['S', 'E']);
                $table->bigInteger('organizer_id')->index('organizer_id');
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('contact_id')->index('contact_id');
                $table->bigInteger('reserved_by')->default(0);
                $table->text('notes');
                $table->string('company_name');
                $table->dateTime('reserved_date');
                $table->enum('status', ['Available', 'Pending', 'Booked'])->default('Available');
                $table->timestamps();
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
