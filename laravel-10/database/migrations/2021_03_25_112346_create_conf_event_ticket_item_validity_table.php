<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventTicketItemValidityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_ticket_item_validity';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('ticket_item_id');
            $table->date('valid_from');
            $table->date('valid_to');
            $table->integer('usage_limit');
            $table->enum('item_type', ['billing', 'checkin']);
            $table->softDeletes();
            $table->timestamps();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('ticket_item_id');
                $table->date('valid_from');
                $table->date('valid_to');
                $table->integer('usage_limit');
                $table->enum('item_type', ['billing', 'checkin']);
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
