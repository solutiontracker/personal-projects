<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEconomicInvoicesTable extends Migration
    {
        const TABLE = 'conf_economic_invoices';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {

            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('bookedInvoiceNumber')->primary();
                $table->date('date')->nullable();
                $table->string('currency', 10)->nullable();
                $table->decimal('exchangeRate', 15)->nullable();
                $table->decimal('netAmount', 15)->nullable();
                $table->decimal('netAmountInBaseCurrency', 15)->nullable();
                $table->decimal('grossAmount', 15)->nullable();
                $table->decimal('grossAmountInBaseCurrency', 15)->nullable();
                $table->decimal('vatAmount', 15)->nullable();
                $table->decimal('roundingAmount', 15)->nullable();
                $table->decimal('remainder', 15)->nullable()->default(0.00);
                $table->integer('remainderInBaseCurrency')->nullable();
                $table->date('dueDate')->nullable();
                $table->integer('paymentTermsNumber')->nullable();
                $table->integer('daysOfCredit')->nullable();
                $table->string('paymentTermsName')->nullable();
                $table->string('paymentTermsType', 50)->nullable();
                $table->integer('customerNumber')->nullable();
                $table->string('recipient_name')->nullable();
                $table->string('recipient_address')->nullable();
                $table->string('recipient_zip', 20)->nullable();
                $table->string('recipient_city', 50)->nullable();
                $table->string('recipient_country')->nullable();
                $table->string('recipient_ean', 50)->nullable();
                $table->integer('customerContactNumber')->nullable();
                $table->integer('vatZoneNumber')->nullable();
                $table->integer('layoutNumber')->nullable();
                $table->decimal('unitNetPrice', 15)->nullable();
                $table->string('delivery_address')->nullable();
                $table->date('deliveryTerms')->nullable()->comment('License from date');
                $table->date('deliveryDate')->nullable()->comment('License to date');
                $table->enum('type', ['1', '2', '3', '4'])->nullable()->comment('1 = (4 + 3), 2 = 5, 3 = Other Groups, 4 = No Groups');
                $table->tinyInteger('is_credit')->nullable()->default(0);
                $table->timestamps();
                $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('bookedInvoiceNumber');
                    $table->date('date')->nullable();
                    $table->string('currency', 10)->nullable();
                    $table->decimal('exchangeRate', 15)->nullable();
                    $table->decimal('netAmount', 15)->nullable();
                    $table->decimal('netAmountInBaseCurrency', 15)->nullable();
                    $table->decimal('grossAmount', 15)->nullable();
                    $table->decimal('grossAmountInBaseCurrency', 15)->nullable();
                    $table->decimal('vatAmount', 15)->nullable();
                    $table->decimal('roundingAmount', 15)->nullable();
                    $table->decimal('remainder', 15)->nullable()->default(0.00);
                    $table->integer('remainderInBaseCurrency')->nullable();
                    $table->date('dueDate')->nullable();
                    $table->integer('paymentTermsNumber')->nullable();
                    $table->integer('daysOfCredit')->nullable();
                    $table->string('paymentTermsName')->nullable();
                    $table->string('paymentTermsType', 50)->nullable();
                    $table->integer('customerNumber')->nullable();
                    $table->string('recipient_name')->nullable();
                    $table->string('recipient_address')->nullable();
                    $table->string('recipient_zip', 20)->nullable();
                    $table->string('recipient_city', 50)->nullable();
                    $table->string('recipient_country')->nullable();
                    $table->string('recipient_ean', 50)->nullable();
                    $table->integer('customerContactNumber')->nullable();
                    $table->integer('vatZoneNumber')->nullable();
                    $table->integer('layoutNumber')->nullable();
                    $table->decimal('unitNetPrice', 15)->nullable();
                    $table->string('delivery_address')->nullable();
                    $table->date('deliveryTerms')->nullable()->comment('License from date');
                    $table->date('deliveryDate')->nullable()->comment('License to date');
                    $table->enum('type', ['1', '2', '3', '4'])->nullable()->comment('1 = (4 + 3), 2 = 5, 3 = Other Groups, 4 = No Groups');
                    $table->tinyInteger('is_credit')->nullable()->default(0);
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
