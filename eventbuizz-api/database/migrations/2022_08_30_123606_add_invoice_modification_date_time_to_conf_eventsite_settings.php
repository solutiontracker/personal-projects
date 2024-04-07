<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceModificationDateTimeToConfEventsiteSettings extends Migration
{
    const TABLE = 'conf_eventsite_settings';
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dateTime('invoice_modification_end_date')->default('0000-00-00 00:00:00');
            $table->time('invoice_modification_end_time')->default('00:00:00');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dateTime('invoice_modification_end_date')->default('0000-00-00 00:00:00');
                $table->time('invoice_modification_end_time')->default('00:00:00');
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
            $table->dropColumn('invoice_modification_end_date');
            $table->dropColumn('invoice_modification_end_time');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('invoice_modification_end_date');
                $table->dropColumn('invoice_modification_end_time');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
