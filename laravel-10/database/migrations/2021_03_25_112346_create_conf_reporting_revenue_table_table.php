<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfReportingRevenueTableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_reporting_revenue_table';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('event_id')->index('event_id');
            $table->text('order_ids');
            $table->text('waiting_order_ids');
            $table->date('date')->index('date');
            $table->bigInteger('total_tickets');
            $table->bigInteger('waiting_tickets')->default(0);
            $table->bigInteger('event_total_tickets');
            $table->double('total_revenue')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('event_id')->index('event_id');
                $table->text('order_ids');
                $table->text('waiting_order_ids');
                $table->date('date')->index('date');
                $table->bigInteger('total_tickets');
                $table->bigInteger('waiting_tickets')->default(0);
                $table->bigInteger('event_total_tickets');
                $table->double('total_revenue')->default(0);
                $table->timestamps();
            $table->softDeletes();
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
        EBSchema::dropDeleteTrigger(self::TABLE);
        Schema::dropIfExists(self::TABLE);
            Schema::connection(config('database.archive_connection'))->dropIfExists(self::TABLE);
    }
}
