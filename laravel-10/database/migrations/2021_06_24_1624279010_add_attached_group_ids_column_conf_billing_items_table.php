<?php
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttachedGroupIdsColumnConfBillingItemsTable extends Migration
{
    const TABLE = 'conf_billing_items';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('attached_group_ids')->nullable();
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('attached_group_ids')->nullable();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('attached_group_ids');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('attached_group_ids');
            });
            
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
