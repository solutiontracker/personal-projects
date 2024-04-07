<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfBillingVouchersTable extends Migration
    {
        const TABLE = 'conf_billing_vouchers';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->enum('type', ['order', 'vat_free', 'billing_items'])->default('order')->index('type');
                $table->tinyInteger('discount_type');
                $table->double('price', 11, 2);
                $table->date('expiry_date');
                $table->string('usage', 45);
                $table->bigInteger('event_id')->index('event_id');
                $table->tinyInteger('status')->index('status');
                $table->integer('qty_status')->nullable()->default(0)->index('qty_status');
                $table->string('code', 250);
                $table->timestamps();
                $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->enum('type', ['order', 'vat_free', 'billing_items'])->default('order')->index('type');
                    $table->tinyInteger('discount_type');
                    $table->double('price', 11, 2);
                    $table->date('expiry_date');
                    $table->string('usage', 45);
                    $table->bigInteger('event_id')->index('event_id');
                    $table->tinyInteger('status')->index('status');
                    $table->integer('qty_status')->nullable()->default(0)->index('qty_status');
                    $table->string('code', 250);
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