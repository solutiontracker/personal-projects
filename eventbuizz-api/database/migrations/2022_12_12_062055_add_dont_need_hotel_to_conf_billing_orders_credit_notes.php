<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDontNeedHotelToConfBillingOrdersCreditNotes extends Migration
{
    const TABLE = 'conf_billing_orders_credit_notes';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('dont_need_hotel')->default(0);
            $table->text('search_history')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('dont_need_hotel')->default(0);
                $table->text('search_history')->nullable();
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
            $table->dropColumn('dont_need_hotel');
            $table->dropColumn('search_history');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('dont_need_hotel');
                $table->dropColumn('search_history');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
