<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventsitePaymentSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_eventsite_payment_settings';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->tinyInteger('dibs_test')->default(0);
            $table->string('dibs_hmac', 500);
            $table->bigInteger('event_id')->index('event_id');
            $table->timestamps();
            $table->softDeletes();
            $table->string('eventsite_merchant_id', 250)->default('12345');
            $table->string('swed_bank_password');
            $table->string('swed_bank_region');
            $table->string('swed_bank_language');
            $table->string('SecretKey', 500);
            $table->string('eventsite_currency', 6)->default('0');
            $table->tinyInteger('eventsite_always_apply_vat')->default(0);
            $table->float('eventsite_vat', 10, 0);
            $table->string('eventsite_vat_countries', 250)->comment('comma separated ids');
            $table->bigInteger('eventsite_invoice_no');
            $table->string('eventsite_invoice_prefix');
            $table->bigInteger('eventsite_invoice_currentnumber');
            $table->string('eventsite_order_prefix');
            $table->tinyInteger('maintain_quantity')->default(0);
            $table->tinyInteger('maintain_quantity_item')->default(0);
            $table->tinyInteger('is_voucher')->default(0);
            $table->integer('billing_merchant_type')->comment('0=DIBS,1=Your Pay');
            $table->string('billing_yourpay_language');
            $table->tinyInteger('billing_type')->default(0);
            $table->tinyInteger('admin_fee_status')->default(0)->comment('0=hide,1=show');
            $table->text('payment_terms');
            $table->text('footer_text');
            $table->tinyInteger('invoice_dimensions')->default(0);
            $table->string('invoice_logo', 250);
            $table->tinyInteger('eventsite_billing')->default(0);
            $table->tinyInteger('eventsite_enable_billing_item_desc')->default(0);
            $table->text('bcc_emails');
            $table->tinyInteger('eventsite_billing_fik')->default(0);
            $table->integer('debitor_number')->default(0);
            $table->tinyInteger('invoice_type')->default(0);
            $table->tinyInteger('auto_invoice');
            $table->string('account_number');
            $table->string('bank_name');
            $table->string('payment_date');
            $table->tinyInteger('billing_item_type')->default(0);
            $table->tinyInteger('eventsite_billing_detail')->default(1);
            $table->bigInteger('max_billing_item_quantity');
            $table->integer('show_business_dating');
            $table->integer('show_subregistration');
            $table->tinyInteger('eventsite_send_email_order_creator')->default(0);
            $table->tinyInteger('evensite_additional_attendee')->default(1);
            $table->tinyInteger('evensite_additional_company')->default(0);
            $table->tinyInteger('evensite_additional_department')->default(0);
            $table->tinyInteger('evensite_additional_organization')->default(0);
            $table->tinyInteger('evensite_additional_phone')->default(0);
            $table->tinyInteger('evensite_additional_custom_fields')->default(0);
            $table->tinyInteger('evensite_additional_title')->default(0);
            $table->tinyInteger('evensite_additional_last_name')->default(0);
            $table->tinyInteger('eventsite_show_email_in_invoice')->default(0);
            $table->tinyInteger('send_credit_note_in_email')->default(0);
            $table->integer('show_hotels')->default(0);
            $table->integer('show_qty_label_free');
            $table->tinyInteger('hotel_person')->default(0);
            $table->double('hotel_vat');
            $table->integer('hotel_vat_status')->default(0);
            $table->date('hotel_from_date')->nullable();
            $table->date('hotel_to_date')->nullable();
            $table->integer('hotel_currency')->default(208);
            $table->integer('show_hotel_prices')->default(1);
            $table->tinyInteger('show_hotel_with_rooms')->default(1);
            $table->string('publicKey');
            $table->string('privateKey');
            $table->string('mistertango_markets');
            $table->date('qty_from_date')->default('0000-00-00');
            $table->tinyInteger('use_qty_rules')->default(0);
            $table->string('qp_agreement_id', 25);
            $table->string('qp_secret_key', 100);
            $table->tinyInteger('qp_auto_capture')->nullable()->default(1);
            $table->string('wc_customer_id', 7)->nullable();
            $table->string('wc_secret', 64)->nullable();
            $table->string('wc_shop_id', 10)->nullable();
            $table->string('stripe_api_key', 500)->nullable();
            $table->string('stripe_secret_key', 500)->nullable();
            $table->tinyInteger('eventsite_apply_multi_vat')->default(0);
            $table->string('bambora_secret_key')->nullable();
            $table->tinyInteger('is_item')->default(1)->comment('this flag use for only free event');
            $table->string('nets_secret_key')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->tinyInteger('dibs_test')->default(0);
                $table->string('dibs_hmac', 500);
                $table->bigInteger('event_id')->index('event_id');
                $table->timestamps();
            $table->softDeletes();
                $table->string('eventsite_merchant_id', 250)->default('12345');
                $table->string('swed_bank_password');
                $table->string('swed_bank_region');
                $table->string('swed_bank_language');
                $table->string('SecretKey', 500);
                $table->string('eventsite_currency', 6)->default('0');
                $table->tinyInteger('eventsite_always_apply_vat')->default(0);
                $table->float('eventsite_vat', 10, 0);
                $table->string('eventsite_vat_countries', 250)->comment('comma separated ids');
                $table->bigInteger('eventsite_invoice_no');
                $table->string('eventsite_invoice_prefix');
                $table->bigInteger('eventsite_invoice_currentnumber');
                $table->string('eventsite_order_prefix');
                $table->tinyInteger('maintain_quantity')->default(0);
                $table->tinyInteger('maintain_quantity_item')->default(0);
                $table->tinyInteger('is_voucher')->default(0);
                $table->integer('billing_merchant_type')->comment('0=DIBS,1=Your Pay');
                $table->string('billing_yourpay_language');
                $table->tinyInteger('billing_type')->default(0);
                $table->tinyInteger('admin_fee_status')->default(0)->comment('0=hide,1=show');
                $table->text('payment_terms');
                $table->text('footer_text');
                $table->tinyInteger('invoice_dimensions')->default(0);
                $table->string('invoice_logo', 250);
                $table->tinyInteger('eventsite_billing')->default(0);
                $table->tinyInteger('eventsite_enable_billing_item_desc')->default(0);
                $table->text('bcc_emails');
                $table->tinyInteger('eventsite_billing_fik')->default(0);
                $table->integer('debitor_number')->default(0);
                $table->tinyInteger('invoice_type')->default(0);
                $table->tinyInteger('auto_invoice');
                $table->string('account_number');
                $table->string('bank_name');
                $table->string('payment_date');
                $table->tinyInteger('billing_item_type')->default(0);
                $table->tinyInteger('eventsite_billing_detail')->default(0);
                $table->bigInteger('max_billing_item_quantity');
                $table->integer('show_business_dating');
                $table->integer('show_subregistration');
                $table->tinyInteger('eventsite_send_email_order_creator')->default(0);
                $table->tinyInteger('evensite_additional_attendee')->default(1);
                $table->tinyInteger('evensite_additional_company')->default(0);
                $table->tinyInteger('evensite_additional_department')->default(0);
                $table->tinyInteger('evensite_additional_organization')->default(0);
                $table->tinyInteger('evensite_additional_phone')->default(0);
                $table->tinyInteger('evensite_additional_custom_fields')->default(0);
                $table->tinyInteger('evensite_additional_title')->default(0);
                $table->tinyInteger('evensite_additional_last_name')->default(0);
                $table->tinyInteger('eventsite_show_email_in_invoice')->default(0);
                $table->tinyInteger('send_credit_note_in_email')->default(0);
                $table->integer('show_hotels')->default(0);
                $table->integer('show_qty_label_free');
                $table->tinyInteger('hotel_person')->default(0);
                $table->double('hotel_vat');
                $table->integer('hotel_vat_status')->default(0);
                $table->date('hotel_from_date')->nullable();
                $table->date('hotel_to_date')->nullable();
                $table->integer('hotel_currency')->default(208);
                $table->integer('show_hotel_prices')->default(1);
                $table->tinyInteger('show_hotel_with_rooms')->default(1);
                $table->string('publicKey');
                $table->string('privateKey');
                $table->string('mistertango_markets');
                $table->date('qty_from_date')->default('0000-00-00');
                $table->tinyInteger('use_qty_rules')->default(0);
                $table->string('qp_agreement_id', 25);
                $table->string('qp_secret_key', 100);
                $table->tinyInteger('qp_auto_capture')->nullable()->default(1);
                $table->string('wc_customer_id', 7)->nullable();
                $table->string('wc_secret', 64)->nullable();
                $table->string('wc_shop_id', 10)->nullable();
                $table->string('stripe_api_key', 500)->nullable();
                $table->string('stripe_secret_key', 500)->nullable();
                $table->tinyInteger('eventsite_apply_multi_vat')->default(0);
                $table->string('bambora_secret_key')->nullable();
                $table->tinyInteger('is_item')->default(1)->comment('this flag use for only free event');
                $table->string('nets_secret_key')->nullable();
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
