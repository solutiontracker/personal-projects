<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHideValidityToConfEventTicketsSettings extends Migration
{
     const TABLE = 'conf_event_tickets_settings';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
             $table->tinyInteger('hide_validity_detail')->default(0);
             $table->tinyInteger('show_item_name')->default(0);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('hide_validity_detail')->default(0);
                $table->tinyInteger('show_item_name')->default(0);  
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
            $table->dropColumn('hide_validity_detail');
            $table->dropColumn('show_item_name');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('hide_validity_detail');
                $table->dropColumn('show_item_name');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
