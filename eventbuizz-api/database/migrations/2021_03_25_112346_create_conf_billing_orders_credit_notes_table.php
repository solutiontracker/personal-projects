<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfBillingOrdersCreditNotesTable extends Migration
    {
        const TABLE = 'conf_billing_orders_credit_notes';

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
                $table->bigInteger('parent_id')->index('parent_id');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->bigInteger('sale_agent_id')->index('sale_agent_id');
                $table->bigInteger('sale_type')->index('sale_type');
                $table->string('session_id')->index('session_id');
                $table->bigInteger('language_id')->default(1)->index('language_id');
                $table->float('event_price', 10, 0);
                $table->integer('is_free')->index('is_free');
                $table->tinyInteger('e_invoice')->default(0);
                $table->integer('event_qty')->default(1);
                $table->float('event_discount', 10, 0);
                $table->string('security', 100);
                $table->float('vat', 10, 0);
                $table->float('vat_amount', 10, 0);
                $table->integer('payment_fee')->nullable();
                $table->float('payment_fee_vat', 10, 0)->nullable();
                $table->string('transaction_id');
                $table->text('invoice_reference_no')->nullable();
                $table->float('grand_total', 10, 0);
                $table->double('reporting_panel_total')->default(0);
                $table->double('corrected_total')->default(0);
                $table->float('summary_sub_total', 10, 0);
                $table->integer('total_attendee');
                $table->string('discount_type', 250);
                $table->string('code', 250);
                $table->integer('coupon_id')->index('coupon_id');
                $table->float('discount_amount', 10, 0);
                $table->double('quantity_discount')->nullable()->default(0);
                $table->dateTime('order_date');
                $table->integer('eventsite_currency');
                $table->string('order_number', 250);
                $table->tinyInteger('billing_quantity')->default(0);
                $table->enum('status', ['completed', 'cancelled', 'pending', 'accepted', 'rejected'])->default('completed')->index('status');
                $table->tinyInteger('is_cancelled_wcn')->default(0);
                $table->text('comments')->nullable();
                $table->tinyInteger('is_voucher');
                $table->tinyInteger('is_payment_received')->default(0)->index('is_payment_received');
                $table->dateTime('payment_received_date')->nullable();
                $table->enum('order_type', ['order', 'invoice'])->default('order')->index('order_type');
                $table->integer('is_waitinglist')->default(0)->index('is_waitinglist');
                $table->integer('is_tango');
                $table->text('dibs_dump');
                $table->longText('user_agent')->nullable();
                $table->longText('session_data')->nullable();
                $table->tinyInteger('is_archive')->default(0)->index('is_archive');
                $table->integer('is_updated')->default(1);
                $table->timestamps();
            $table->softDeletes();
                $table->tinyInteger('hide_first_billing_item_description')->default(0);
                $table->dateTime('credit_note_create_date');
                $table->tinyInteger('is_added_reporting')->default(0)->index('is_added_reporting');
                $table->tinyInteger('to_be_fetched')->default(1)->index('to_be_fetched');
                $table->tinyInteger('new_imp_flag')->nullable()->index('new_imp_flag');
                $table->tinyInteger('is_updated_qty');
                $table->tinyInteger('item_level_vat')->default(0);
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->bigInteger('order_id')->index('order_id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->bigInteger('parent_id')->index('parent_id');
                    $table->bigInteger('attendee_id')->index('attendee_id');
                    $table->bigInteger('sale_agent_id')->index('sale_agent_id');
                    $table->bigInteger('sale_type')->index('sale_type');
                    $table->string('session_id')->index('session_id');
                    $table->bigInteger('language_id')->default(1)->index('language_id');
                    $table->float('event_price', 10, 0);
                    $table->integer('is_free')->index('is_free');
                    $table->tinyInteger('e_invoice')->default(0);
                    $table->integer('event_qty')->default(1);
                    $table->float('event_discount', 10, 0);
                    $table->string('security', 100);
                    $table->float('vat', 10, 0);
                    $table->float('vat_amount', 10, 0);
                    $table->integer('payment_fee')->nullable();
                    $table->float('payment_fee_vat', 10, 0)->nullable();
                    $table->string('transaction_id');
                    $table->text('invoice_reference_no')->nullable();
                    $table->float('grand_total', 10, 0);
                    $table->double('reporting_panel_total')->default(0);
                    $table->double('corrected_total')->default(0);
                    $table->float('summary_sub_total', 10, 0);
                    $table->integer('total_attendee');
                    $table->string('discount_type', 250);
                    $table->string('code', 250);
                    $table->integer('coupon_id')->index('coupon_id');
                    $table->float('discount_amount', 10, 0);
                    $table->double('quantity_discount')->nullable()->default(0);
                    $table->dateTime('order_date');
                    $table->integer('eventsite_currency');
                    $table->string('order_number', 250);
                    $table->tinyInteger('billing_quantity')->default(0);
                    $table->enum('status', ['completed', 'cancelled', 'pending', 'accepted', 'rejected'])->default('completed')->index('status');
                    $table->tinyInteger('is_cancelled_wcn')->default(0);
                    $table->text('comments')->nullable();
                    $table->tinyInteger('is_voucher');
                    $table->tinyInteger('is_payment_received')->default(0)->index('is_payment_received');
                    $table->dateTime('payment_received_date')->nullable();
                    $table->enum('order_type', ['order', 'invoice'])->default('order')->index('order_type');
                    $table->integer('is_waitinglist')->default(0)->index('is_waitinglist');
                    $table->integer('is_tango');
                    $table->text('dibs_dump');
                    $table->longText('user_agent')->nullable();
                    $table->longText('session_data')->nullable();
                    $table->tinyInteger('is_archive')->default(0)->index('is_archive');
                    $table->integer('is_updated')->default(1);
                    $table->timestamps();
            $table->softDeletes();
                    $table->tinyInteger('hide_first_billing_item_description')->default(0);
                    $table->dateTime('credit_note_create_date');
                    $table->tinyInteger('is_added_reporting')->default(0)->index('is_added_reporting');
                    $table->tinyInteger('to_be_fetched')->default(1)->index('to_be_fetched');
                    $table->tinyInteger('new_imp_flag')->nullable()->index('new_imp_flag');
                    $table->tinyInteger('is_updated_qty');
                    $table->tinyInteger('item_level_vat')->default(0);
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
