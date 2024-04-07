<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEconomicProductsTable extends Migration
    {
        const TABLE = 'conf_economic_products';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('productNumber', 50);
                $table->string('name')->nullable();
                $table->text('description')->nullable();
                $table->decimal('recommendedPrice', 15)->nullable();
                $table->decimal('salesPrice', 15)->nullable();
                $table->dateTime('lastUpdated')->nullable();
                $table->integer('productGroupNumber')->nullable();
                $table->tinyInteger('barred')->nullable()->default(0);
                $table->timestamps();
                $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->string('productNumber', 50);
                    $table->string('name')->nullable();
                    $table->text('description')->nullable();
                    $table->decimal('recommendedPrice', 15)->nullable();
                    $table->decimal('salesPrice', 15)->nullable();
                    $table->dateTime('lastUpdated')->nullable();
                    $table->integer('productGroupNumber')->nullable();
                    $table->tinyInteger('barred')->nullable()->default(0);
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
