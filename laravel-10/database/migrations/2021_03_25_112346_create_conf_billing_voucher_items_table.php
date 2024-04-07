<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfBillingVoucherItemsTable extends Migration
    {
        const TABLE = 'conf_billing_voucher_items';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('voucher_id')->index('voucher_id');
                $table->unsignedTinyInteger('discount_type')->nullable()->index('discount_type');
                $table->float('price', 10, 0)->unsigned()->nullable()->default(0);
                $table->integer('useage')->default(0);
                $table->bigInteger('item_id')->index('item_id');
                $table->enum('item_type', ['event', 'addon'])->index('item_type');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->bigInteger('voucher_id')->index('voucher_id');
                    $table->unsignedTinyInteger('discount_type')->nullable()->index('discount_type');
                    $table->float('price', 10, 0)->unsigned()->nullable()->default(0);
                    $table->integer('useage')->default(0);
                    $table->bigInteger('item_id')->index('item_id');
                    $table->enum('item_type', ['event', 'addon'])->index('item_type');
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
