<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddToAfterStockedToWaitinglistToConfEventWaitingListSettings extends Migration
{
    const TABLE = 'conf_event_waiting_list_settings';

    /**
     * Run the migrations.
     *
     * @return void 
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->integer('registration_form_id')->default(0);
            $table->tinyInteger('after_stocked_to_waitinglist')->default(0);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->integer('registration_form_id')->default(0);
                $table->tinyInteger('after_stocked_to_waitinglist')->default(0);
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
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('registration_form_id');
            $table->dropColumn('after_stocked_to_waitinglist');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('registration_form_id');
                $table->dropColumn('after_stocked_to_waitinglist');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

}
