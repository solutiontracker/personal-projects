<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfStandSaleRegistrationLinksTable extends Migration
{
    const TABLE = 'conf_stand_sale_registration_links';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('event_id')->index('event_id')->nullable();
            $table->integer('order_id')->index('order_id')->nullable();
            $table->enum('type', ['exhibitor', 'sponsor'])->index('actor_type');
            $table->integer('link_id')->index('link_id')->comment('Exhibitor|Sponsor')->nullable();
            $table->string('token', 30)->nullable();
            $table->dateTime('expire_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {
            
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('event_id')->index('event_id')->nullable();
                $table->integer('order_id')->index('order_id')->nullable();
                $table->enum('type', ['exhibitor', 'sponsor'])->index('actor_type');
                $table->integer('link_id')->index('link_id')->comment('Exhibitor|Sponsor')->nullable();
                $table->string('token', 30)->nullable();
                $table->dateTime('expire_at')->nullable();
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
