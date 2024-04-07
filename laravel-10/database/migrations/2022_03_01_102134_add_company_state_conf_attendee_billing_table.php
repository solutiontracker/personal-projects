<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddCompanyStateConfAttendeeBillingTable extends Migration
{
    const TABLE = 'conf_attendee_billing';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('billing_company_street_2')->nullable();
            $table->string('billing_company_state')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('billing_company_street_2')->nullable();
                $table->string('billing_company_state')->nullable();
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
            $table->dropColumn('billing_company_street_2');
            $table->dropColumn('billing_company_state');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('billing_company_street_2');
                $table->dropColumn('billing_company_state');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
