<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfAttendeeBillingTable extends Migration
{
    const TABLE = 'conf_attendee_billing';

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
            $table->bigInteger('organizer_id')->index('FK_ORGANIZER_ID');
            $table->bigInteger('event_id')->index('event_id');
            $table->bigInteger('attendee_id')->index('attendee_id');
            $table->tinyInteger('billing_membership')->nullable();
            $table->string('billing_member_number')->nullable();
            $table->string('billing_private_street', 250)->nullable();
            $table->string('billing_private_house_number', 250)->nullable();
            $table->string('billing_private_post_code', 250)->nullable();
            $table->string('billing_private_city', 250)->nullable();
            $table->string('billing_private_country', 250)->nullable();
            $table->string('billing_company_type', 100)->nullable();
            $table->string('billing_company_registration_number', 250)->nullable();
            $table->string('billing_ean', 250)->nullable();
            $table->string('billing_contact_person_name', 250)->nullable();
            $table->string('billing_contact_person_email', 250)->nullable();
            $table->string('billing_contact_person_mobile_number', 250)->nullable();
            $table->string('billing_company_street', 250)->nullable();
            $table->string('billing_company_house_number', 250)->nullable();
            $table->string('billing_company_post_code', 250)->nullable();
            $table->string('billing_company_city', 250)->nullable();
            $table->string('billing_company_country', 250)->nullable();
            $table->string('billing_poNumber', 250)->nullable();
            $table->string('billing_company_invoice_payer_street_house_number', 250)->nullable();
            $table->string('billing_company_invoice_payer_company_name', 250)->nullable();
            $table->string('billing_company_invoice_payer_post_code', 250)->nullable();
            $table->string('billing_company_invoice_payer_city', 250)->nullable();
            $table->string('billing_company_invoice_payer_country', 250)->nullable();
            $table->text('invoice_reference_no')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->tinyInteger('is_updated_order')->default(0);
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('order_id');
                $table->bigInteger('organizer_id');
                $table->bigInteger('event_id');
                $table->bigInteger('attendee_id');
                $table->tinyInteger('billing_membership')->nullable();
                $table->string('billing_member_number')->nullable();
                $table->string('billing_private_street', 250)->nullable();
                $table->string('billing_private_house_number', 250)->nullable();
                $table->string('billing_private_post_code', 250)->nullable();
                $table->string('billing_private_city', 250)->nullable();
                $table->string('billing_private_country', 250)->nullable();
                $table->string('billing_company_type', 100)->nullable();
                $table->string('billing_company_registration_number', 250)->nullable();
                $table->string('billing_ean', 250)->nullable();
                $table->string('billing_contact_person_name', 250)->nullable();
                $table->string('billing_contact_person_email', 250)->nullable();
                $table->string('billing_contact_person_mobile_number', 250)->nullable();
                $table->string('billing_company_street', 250)->nullable();
                $table->string('billing_company_house_number', 250)->nullable();
                $table->string('billing_company_post_code', 250)->nullable();
                $table->string('billing_company_city', 250)->nullable();
                $table->string('billing_company_country', 250)->nullable();
                $table->string('billing_poNumber', 250)->nullable();
                $table->text('invoice_reference_no')->nullable();
                $table->timestamps();
            $table->softDeletes();
                $table->tinyInteger('is_updated_order')->default(0);
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
