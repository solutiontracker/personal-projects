<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEconomicCustomersTable extends Migration
    {
        const TABLE = 'conf_economic_customers';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('customerNumber');
                $table->string('email')->nullable();
                $table->string('currency', 5)->nullable();
                $table->integer('paymentTermsNumber')->nullable();
                $table->integer('customerGroupNumber')->nullable();
                $table->string('address')->nullable();
                $table->decimal('balance', 15)->nullable();
                $table->decimal('dueAmount', 15)->nullable();
                $table->string('corporateIdentificationNumber')->nullable();
                $table->string('city', 100)->nullable();
                $table->string('country', 100)->nullable();
                $table->string('ean', 100)->nullable();
                $table->string('name', 100)->nullable();
                $table->string('zip', 20)->nullable();
                $table->string('website')->nullable();
                $table->integer('vatZoneNumber')->nullable();
                $table->integer('layoutNumber')->nullable();
                $table->integer('customerContactNumber')->nullable();
                $table->dateTime('lastUpdated')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('customerNumber');
                    $table->string('email')->nullable();
                    $table->string('currency', 5)->nullable();
                    $table->integer('paymentTermsNumber')->nullable();
                    $table->integer('customerGroupNumber')->nullable();
                    $table->string('address')->nullable();
                    $table->decimal('balance', 15)->nullable();
                    $table->decimal('dueAmount', 15)->nullable();
                    $table->string('corporateIdentificationNumber')->nullable();
                    $table->string('city', 100)->nullable();
                    $table->string('country', 100)->nullable();
                    $table->string('ean', 100)->nullable();
                    $table->string('name', 100)->nullable();
                    $table->string('zip', 20)->nullable();
                    $table->string('website')->nullable();
                    $table->integer('vatZoneNumber')->nullable();
                    $table->integer('layoutNumber')->nullable();
                    $table->integer('customerContactNumber')->nullable();
                    $table->dateTime('lastUpdated')->nullable();
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
