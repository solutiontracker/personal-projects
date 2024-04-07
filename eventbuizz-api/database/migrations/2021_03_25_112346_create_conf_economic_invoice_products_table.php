<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfEconomicInvoiceProductsTable extends Migration
{
    const TABLE = 'conf_economic_invoice_products';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('lineNumber')->nullable();
            $table->integer('sortKey')->nullable();
            $table->text('description')->nullable();
            $table->decimal('quantity', 11)->nullable();
            $table->decimal('unitNetPrice', 11)->nullable();
            $table->decimal('discountPercentage', 11)->nullable();
            $table->decimal('unitCostPrice', 11)->nullable();
            $table->decimal('vatRate', 11)->nullable();
            $table->decimal('totalNetAmount', 11)->nullable();
            $table->integer('productNumber')->nullable();
            $table->integer('productGroupNumber')->nullable();
            $table->integer('bookedInvoiceNumber')->nullable();
            $table->integer('unitNumber')->nullable();
            $table->integer('departmentalDistributionNumber')->nullable();
            $table->tinyInteger('is_credit')->nullable()->default(0);
            $table->integer('customerNumber')->nullable();
            $table->date('deliveryTerms')->nullable()->comment('License from date');
            $table->date('deliveryDate')->nullable()->comment('License to date');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('lineNumber')->nullable();
                $table->integer('sortKey')->nullable();
                $table->text('description')->nullable();
                $table->decimal('quantity', 11)->nullable();
                $table->decimal('unitNetPrice', 11)->nullable();
                $table->decimal('discountPercentage', 11)->nullable();
                $table->decimal('unitCostPrice', 11)->nullable();
                $table->decimal('vatRate', 11)->nullable();
                $table->decimal('totalNetAmount', 11)->nullable();
                $table->integer('productNumber')->nullable();
                $table->integer('productGroupNumber')->nullable();
                $table->integer('bookedInvoiceNumber')->nullable();
                $table->integer('unitNumber')->nullable();
                $table->integer('departmentalDistributionNumber')->nullable();
                $table->tinyInteger('is_credit')->nullable()->default(0);
                $table->integer('customerNumber')->nullable();
                $table->date('deliveryTerms')->nullable()->comment('License from date');
                $table->date('deliveryDate')->nullable()->comment('License to date');
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
