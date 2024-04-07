<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class UpdateOrderForConfBillingOrdersTable extends Migration
{
    const TABLE = 'conf_billing_orders';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            DB::statement("ALTER TABLE conf_billing_orders MODIFY COLUMN order_for ENUM('attendee', 'exhibitor', 'sponsor')");
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                DB::statement("ALTER TABLE conf_billing_orders MODIFY COLUMN order_for ENUM('attendee', 'exhibitor', 'sponsor')");
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            DB::statement("ALTER TABLE conf_billing_orders MODIFY COLUMN order_for ENUM('attendee', 'exhibitor')");
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                DB::statement("ALTER TABLE conf_billing_orders MODIFY COLUMN order_for ENUM('attendee', 'exhibitor')");
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
