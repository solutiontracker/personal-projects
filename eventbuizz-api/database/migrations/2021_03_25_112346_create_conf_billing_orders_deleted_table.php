<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfBillingOrdersDeletedTable extends Migration
    {
        const TABLE = 'conf_billing_orders_deleted';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->bigInteger('order_id')->index('order_id');
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->string('session_id');
                $table->float('event_price', 10, 0);
                $table->integer('event_qty')->default(1);
                $table->string('security', 100);
                $table->float('vat', 10, 0);
                $table->float('vat_amount', 10, 0);
                $table->integer('payment_fee');
                $table->string('transaction_id');
                $table->float('grand_total', 10, 0);
                $table->float('summary_sub_total', 10, 0);
                $table->integer('total_attendee');
                $table->string('discount_type', 250);
                $table->string('code', 250);
                $table->integer('coupon_id');
                $table->float('discount_amount', 10, 0);
                $table->dateTime('order_date');
                $table->dateTime('deleted_date');
                $table->integer('eventsite_currency');
                $table->string('order_number', 250);
                $table->tinyInteger('billing_quantity')->default(0);
                $table->enum('status', ['completed', 'cancelled', 'pending', 'accepted', 'rejected'])->default('completed');
                $table->text('comments')->nullable();
                $table->text('addons_detail');
                $table->text('attendees_detail');
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->bigInteger('order_id')->index('order_id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->bigInteger('attendee_id')->index('attendee_id');
                    $table->string('session_id');
                    $table->float('event_price', 10, 0);
                    $table->integer('event_qty')->default(1);
                    $table->string('security', 100);
                    $table->float('vat', 10, 0);
                    $table->float('vat_amount', 10, 0);
                    $table->integer('payment_fee');
                    $table->string('transaction_id');
                    $table->float('grand_total', 10, 0);
                    $table->float('summary_sub_total', 10, 0);
                    $table->integer('total_attendee');
                    $table->string('discount_type', 250);
                    $table->string('code', 250);
                    $table->integer('coupon_id');
                    $table->float('discount_amount', 10, 0);
                    $table->dateTime('order_date');
                    $table->dateTime('deleted_date');
                    $table->integer('eventsite_currency');
                    $table->string('order_number', 250);
                    $table->tinyInteger('billing_quantity')->default(0);
                    $table->enum('status', ['completed', 'cancelled', 'pending', 'accepted', 'rejected'])->default('completed');
                    $table->text('comments')->nullable();
                    $table->text('addons_detail');
                    $table->text('attendees_detail');
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
