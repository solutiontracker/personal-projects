<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShowCounterOnProjectorToConfEventTurnListSettings extends Migration
{
    const TABLE = 'conf_event_turn_list_settings';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger("show_speaker_counter_on_projector")->default(1);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger("show_speaker_counter_on_projector")->default(1);
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
            $table->dropColumn('show_speaker_counter_on_projector');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('show_speaker_counter_on_projector');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
