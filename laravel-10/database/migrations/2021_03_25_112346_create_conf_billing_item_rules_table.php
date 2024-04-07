<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfBillingItemRulesTable extends Migration
    {
        const TABLE = 'conf_billing_item_rules';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->bigInteger('item_id')->index('item_id_2');
                $table->enum('rule_type', ['qty', 'date'])->index('rule_type');
                $table->enum('discount_type', ['percentage', 'price'])->index('discount_type');
                $table->double('discount', 11, 2);
                $table->double('price', 11, 2);
                $table->date('start_date')->index('start_date');
                $table->date('end_date')->index('end_date');
                $table->bigInteger('qty');
                $table->bigInteger('event_id')->index('event_id');
                $table->timestamps();
                $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->bigInteger('item_id')->index('item_id_2');
                    $table->enum('rule_type', ['qty', 'date'])->index('rule_type');
                    $table->enum('discount_type', ['percentage', 'price'])->index('discount_type');
                    $table->double('discount', 11, 2);
                    $table->double('price', 11, 2);
                    $table->date('start_date')->index('start_date');
                    $table->date('end_date')->index('end_date');
                    $table->bigInteger('qty');
                    $table->bigInteger('event_id')->index('event_id');
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
