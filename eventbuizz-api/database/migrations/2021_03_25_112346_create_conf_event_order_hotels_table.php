<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventOrderHotelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_order_hotels';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->integer('hotel_id')->index('Hotel Id');
            $table->integer('order_id')->index('Order Id');
            $table->string('name');
            $table->double('price', 11, 2);
            $table->enum('price_type', ['fixed', 'notfixed']);
            $table->double('vat', 11, 2)->nullable()->default(0);
            $table->double('vat_price', 11, 2)->nullable()->default(0);
            $table->integer('rooms')->nullable();
            $table->date('checkin');
            $table->date('checkout');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->integer('hotel_id')->index('Hotel Id');
                $table->integer('order_id')->index('Order Id');
                $table->string('name');
                $table->double('price', 11, 2);
                $table->enum('price_type', ['fixed', 'notfixed']);
                $table->double('vat', 11, 2)->nullable()->default(0);
                $table->double('vat_price', 11, 2)->nullable()->default(0);
                $table->integer('rooms')->nullable();
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
