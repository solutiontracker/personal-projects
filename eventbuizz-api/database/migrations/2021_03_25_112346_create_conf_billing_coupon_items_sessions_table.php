<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfBillingCouponItemsSessionsTable extends Migration
    {
        const TABLE = 'conf_billing_coupon_items_sessions';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('event_id')->nullable()->index('event_id');
                $table->string('session_id')->nullable()->index('session_id');
                $table->unsignedBigInteger('coupon_id')->nullable()->index('coupon_id');
                $table->dateTime('date_reserved')->nullable();
                $table->integer('usage')->default(0);
                $table->unsignedTinyInteger('status')->nullable()->default(0)->index('status')->comment('0=pending,0=active');
                $table->bigInteger('addon_id')->index('addon_id');
                $table->enum('addon_type', ['event', 'addon']);
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->unsignedBigInteger('event_id')->nullable()->index('event_id');
                    $table->string('session_id')->nullable()->index('session_id');
                    $table->unsignedBigInteger('coupon_id')->nullable()->index('coupon_id');
                    $table->dateTime('date_reserved')->nullable();
                    $table->integer('usage')->default(0);
                    $table->unsignedTinyInteger('status')->nullable()->default(0)->index('status')->comment('0=pending,0=active');
                    $table->bigInteger('addon_id')->index('addon_id');
                    $table->enum('addon_type', ['event', 'addon']);
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
