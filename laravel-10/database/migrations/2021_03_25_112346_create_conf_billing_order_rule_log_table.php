<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfBillingOrderRuleLogTable extends Migration
    {
        const TABLE = 'conf_billing_order_rule_log';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->bigInteger('rule_id')->index('rule_id');
                $table->bigInteger('order_id')->index('order_id');
                $table->bigInteger('item_id')->index('item_id');
                $table->bigInteger('item_qty');
                $table->bigInteger('rule_qty');
                $table->double('discount_type');
                $table->double('item_price');
                $table->double('rule_discount');
                $table->double('item_discount');
                $table->timestamps();
                $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->bigInteger('rule_id')->index('rule_id');
                    $table->bigInteger('order_id')->index('order_id');
                    $table->bigInteger('item_id')->index('item_id');
                    $table->bigInteger('item_qty');
                    $table->bigInteger('rule_qty');
                    $table->double('discount_type');
                    $table->double('item_price');
                    $table->double('rule_discount');
                    $table->double('item_discount');
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
