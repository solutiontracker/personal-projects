<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderForToConfBillingOrdersTable extends Migration
{
    const TABLE = 'conf_billing_orders';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->enum('order_for',['attendee','exhibitor'])->default('attendee')->after('item_level_vat');
            $table->bigInteger('order_for_id')->unsigned();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->enum('order_for',['attendee','exhibitor'])->default('attendee')->after('item_level_vat');
                $table->bigInteger('order_for_id')->unsigned();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('order_for');
            $table->dropColumn('order_for_id');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('order_for');
                $table->dropColumn('order_for_id');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
