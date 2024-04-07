<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_tickets';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->text('serial');
            $table->integer('event_id');
            $table->integer('addon_id');
            $table->integer('ticket_item_id');
            $table->string('addon_type', 200)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('qr_string', 200)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->text('serial');
                $table->integer('event_id');
                $table->integer('addon_id');
                $table->integer('ticket_item_id');
                $table->string('addon_type', 200)->nullable();
                $table->tinyInteger('status')->default(1);
                $table->string('qr_string', 200)->nullable();
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
