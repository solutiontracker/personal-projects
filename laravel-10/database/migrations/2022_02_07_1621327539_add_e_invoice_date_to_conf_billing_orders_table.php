<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEInvoiceDateToConfBillingOrdersTable extends Migration
{
    const TABLE = 'conf_billing_orders';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dateTime('e_invoice_date')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dateTime('e_invoice_date')->nullable();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('e_invoice_date');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('e_invoice_date');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
