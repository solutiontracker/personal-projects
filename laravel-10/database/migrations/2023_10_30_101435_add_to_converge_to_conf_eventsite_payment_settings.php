<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToConvergeToConfEventsitePaymentSettings extends Migration
{
    const TABLE = 'conf_eventsite_payment_settings';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('converge_public_key')->default(0)->nullable();
            $table->string('converge_secret_key')->default(0)->nullable();
            $table->string('converge_merchant_alias')->default(0)->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('converge_public_key')->default(0)->nullable();
                $table->string('converge_secret_key')->default(0)->nullable();
                $table->string('converge_merchant_alias')->default(0)->nullable();
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
            $table->dropColumn('converge_public_key');
            $table->dropColumn('converge_secret_key');
            $table->dropColumn('converge_merchant_alias');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('converge_public_key');
                $table->dropColumn('converge_secret_key');
                $table->dropColumn('converge_merchant_alias');
            });
            
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
        
    }
}
