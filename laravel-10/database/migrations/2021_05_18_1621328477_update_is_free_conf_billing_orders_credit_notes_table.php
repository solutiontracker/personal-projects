<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class UpdateIsFreeConfBillingOrdersCreditNotesTable extends Migration
{
    const TABLE = 'conf_billing_orders_credit_notes';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->smallInteger('is_free')->change();
            $table->smallInteger('is_waitinglist')->change();
            $table->smallInteger('is_tango')->change();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->smallInteger('is_free')->change();
                $table->smallInteger('is_waitinglist')->change();
                $table->smallInteger('is_tango')->change();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->integer('is_free')->change();
            $table->integer('is_waitinglist')->change();
            $table->integer('is_tango')->change();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->integer('is_free')->change();
                $table->integer('is_waitinglist')->change();
                $table->integer('is_tango')->change();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
