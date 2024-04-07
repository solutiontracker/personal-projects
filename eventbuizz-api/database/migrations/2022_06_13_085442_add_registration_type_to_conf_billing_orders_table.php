<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegistrationTypeToConfBillingOrdersTable extends Migration
{
    const TABLE = 'conf_billing_orders';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->integer('registration_type_id')->nullable();
            $table->enum('registration_type', ['exhibitor', 'sponsor'])->index('registration_type')->nullable();

        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('registration_type_id')->nullable();
                $table->enum('registration_type', ['exhibitor', 'sponsor'])->index('registration_type')->nullable();
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
            $table->dropColumn('registration_type_id');
            $table->dropColumn('registration_type');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('registration_type_id');
                $table->dropColumn('registration_type');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
