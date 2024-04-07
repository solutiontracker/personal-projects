<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventTicketItemConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_ticket_item_config';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('event_id');
            $table->integer('ticket_item_id')->nullable();
            $table->string('serial_start', 60)->nullable()->default('0001');
            $table->string('prefix', 6)->nullable()->default('EB');
            $table->softDeletes();
            $table->timestamps();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('event_id');
                $table->integer('ticket_item_id')->nullable();
                $table->string('serial_start', 60)->nullable()->default('0001');
                $table->string('prefix', 6)->nullable()->default('EB');
                $table->softDeletes();
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
