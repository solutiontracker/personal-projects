<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfBillingOrderAddonsTable extends Migration
    {
        const TABLE = 'conf_billing_order_addons';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('order_id')->index('order_id');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->integer('addon_id')->index('addon_id');
                $table->string('name', 250);
                $table->double('price', 11, 2);
                $table->double('vat', 11, 2)->default(0);
                $table->integer('qty')->default(1);
                $table->double('discount', 11, 2);
                $table->integer('discount_qty');
                $table->tinyInteger('discount_type');
                $table->integer('ticket_item_id')->nullable()->index('ticket_item_id');
                $table->bigInteger('parent')->index('parent');
                $table->enum('link_to', ['none', 'program', 'track', 'workshop', 'attendee_group'])->default('none')->index('link_to');
                $table->string('link_to_id', 100)->nullable()->index('link_to_id');
                $table->integer('group_id')->default(0)->index('group_id');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('order_id')->index('order_id');
                    $table->bigInteger('attendee_id')->index('attendee_id');
                    $table->integer('addon_id')->index('addon_id');
                    $table->string('name', 250);
                    $table->double('price', 11, 2);
                    $table->double('vat', 11, 2)->default(0);
                    $table->integer('qty')->default(1);
                    $table->double('discount', 11, 2);
                    $table->integer('discount_qty');
                    $table->tinyInteger('discount_type');
                    $table->integer('ticket_item_id')->nullable()->index('ticket_item_id');
                    $table->bigInteger('parent')->index('parent');
                    $table->enum('link_to', ['none', 'program', 'track', 'workshop', 'attendee_group'])->default('none')->index('link_to');
                    $table->string('link_to_id', 100)->nullable()->index('link_to_id');
                    $table->integer('group_id')->default(0)->index('group_id');
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
