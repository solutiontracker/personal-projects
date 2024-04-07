<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventCreditNoteOrderHotelsTable extends Migration
    {
        const TABLE = 'conf_event_credit_note_order_hotels';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->bigInteger('hotel_id')->index('hotel_id');
                $table->bigInteger('order_id')->index('order_id');
                $table->string('name');
                $table->bigInteger('price');
                $table->enum('price_type', ['fixed', 'notfixed']);
                $table->double('vat');
                $table->double('vat_price');
                $table->integer('rooms');
                $table->date('checkin');
                $table->date('checkout');
                $table->timestamps();
                $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->bigInteger('hotel_id')->index('hotel_id');
                    $table->bigInteger('order_id')->index('order_id');
                    $table->string('name');
                    $table->bigInteger('price');
                    $table->enum('price_type', ['fixed', 'notfixed']);
                    $table->double('vat');
                    $table->double('vat_price');
                    $table->integer('rooms');
                    $table->date('checkin');
                    $table->date('checkout');
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