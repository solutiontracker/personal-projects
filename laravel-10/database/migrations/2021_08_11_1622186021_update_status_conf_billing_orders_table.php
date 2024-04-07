<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class UpdateStatusConfBillingOrdersTable extends Migration
{
    const TABLE = 'conf_billing_orders';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            DB::statement("ALTER TABLE conf_billing_orders MODIFY COLUMN status ENUM('completed', 'cancelled', 'pending', 'accepted', 'rejected', 'draft', 'awaiting_payment')");
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                DB::statement("ALTER TABLE conf_billing_orders MODIFY COLUMN status ENUM('completed', 'cancelled', 'pending', 'accepted', 'rejected', 'draft', 'awaiting_payment')");
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            DB::statement("ALTER TABLE conf_billing_orders MODIFY COLUMN status ENUM('completed', 'cancelled', 'pending', 'accepted', 'rejected')");
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                DB::statement("ALTER TABLE conf_billing_orders MODIFY COLUMN status ENUM('completed', 'cancelled', 'pending', 'accepted', 'rejected')");
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
