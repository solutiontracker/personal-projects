<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfBillingSessionOrdersTable extends Migration
    {
        const TABLE = 'conf_billing_session_orders';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->longText('order_data');
                $table->string('dibs_transaction_id', 250)->index('dibs_transaction_id');
                $table->enum('order_status', ['completed', 'cancelled', 'pending', 'accepted', 'rejected'])->default('pending')->index('order_status');
                $table->dateTime('order_datetime');
                $table->string('db_session_id', 250)->index('db_session_id');
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('organizer_id')->index('organizer_id');
                $table->bigInteger('order_id')->index('order_id');
                $table->bigInteger('temp_order_id')->index('temp_order_id');
                $table->float('amount', 10, 0);
                $table->integer('offline_payment')->default(0);
                $table->string('swed_bank_order_id')->index('swed_bank_order_id');
                $table->string('swed_bank_reference_id')->index('swed_bank_reference_id');
                $table->integer('waitinglist_order_id')->nullable()->default(0)->index('waitinglist_order_id');
                $table->tinyInteger('cron_checked')->nullable()->default(0)->comment('flag to identify orders already checked for missing data by cron job');
                $table->tinyInteger('order_missing')->nullable()->default(0)->comment('flag for orders which do not have entry in billing orders table.');
                $table->enum('status', ['0', '1', '2'])->default('0')->index('status')->comment('1=pending, 0=processing, 3=processed');
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->longText('order_data');
                    $table->string('dibs_transaction_id', 250)->index('dibs_transaction_id');
                    $table->enum('order_status', ['completed', 'cancelled', 'pending', 'accepted', 'rejected'])->default('pending')->index('order_status');
                    $table->dateTime('order_datetime');
                    $table->string('db_session_id', 250)->index('db_session_id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->bigInteger('organizer_id')->index('organizer_id');
                    $table->bigInteger('order_id')->index('order_id');
                    $table->bigInteger('temp_order_id')->index('temp_order_id');
                    $table->float('amount', 10, 0);
                    $table->integer('offline_payment')->default(0);
                    $table->string('swed_bank_order_id')->index('swed_bank_order_id');
                    $table->string('swed_bank_reference_id')->index('swed_bank_reference_id');
                    $table->integer('waitinglist_order_id')->nullable()->default(0)->index('waitinglist_order_id');
                    $table->tinyInteger('cron_checked')->nullable()->default(0)->comment('flag to identify orders already checked for missing data by cron job');
                    $table->tinyInteger('order_missing')->nullable()->default(0)->comment('flag for orders which do not have entry in billing orders table.');
                    $table->enum('status', ['0', '1', '2'])->default('0')->index('status')->comment('1=pending, 0=processing, 3=processed');
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
