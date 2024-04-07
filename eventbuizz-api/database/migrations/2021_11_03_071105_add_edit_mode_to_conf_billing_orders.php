<?php
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEditModeToConfBillingOrders extends Migration
{
    const TABLE = 'conf_billing_orders';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('edit_mode')->index('edit_mode');
            $table->longText('payment_response')->nullable();
            $table->bigInteger('registration_form_id')->nullable()->default(0)->index('registration_form_id');
            $table->tinyInteger('is_credit_note')->default(0)->nullable();
            $table->bigInteger('clone_of')->default(0)->nullable();
            $table->tinyInteger('is_new_flow')->default(0)->nullable();
            $table->dateTime('draft_at')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('edit_mode')->index('edit_mode');
                $table->longText('payment_response')->nullable();
                $table->bigInteger('registration_form_id')->nullable()->default(0)->index('registration_form_id');
                $table->tinyInteger('is_credit_note')->default(0)->nullable();
                $table->bigInteger('clone_of')->default(0)->nullable();
                $table->tinyInteger('is_new_flow')->default(0)->nullable();
                $table->dateTime('draft_at')->nullable();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('edit_mode');
            $table->dropColumn('payment_response');
            $table->dropColumn('registration_form_id');
            $table->dropColumn('is_credit_note');
            $table->dropColumn('clone_of');
            $table->dropColumn('is_new_flow');
            $table->dropColumn('draft_at');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('edit_mode');
                $table->dropColumn('payment_response');
                $table->dropColumn('registration_form_id');
                $table->dropColumn('is_credit_note');
                $table->dropColumn('clone_of');
                $table->dropColumn('is_new_flow');
                $table->dropColumn('draft_at');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
