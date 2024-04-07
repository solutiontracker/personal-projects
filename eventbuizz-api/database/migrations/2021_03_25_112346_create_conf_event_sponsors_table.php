<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventSponsorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_sponsors';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('event_id')->index('event_id');
            $table->string('name', 100);
            $table->string('email');
            $table->string('logo', 100);
            $table->string('booth', 100);
            $table->string('phone_number', 100)->nullable();
            $table->string('website');
            $table->string('twitter');
            $table->string('facebook');
            $table->string('linkedin');
            $table->tinyInteger('stype')->default(0)->index('stype')->comment('1= Gold, 2= Silver');
            $table->enum('allow_reservations', ['Y', 'N'])->default('N');
            $table->tinyInteger('status')->index('status');
            $table->tinyInteger('allow_card_reader')->default(0);
            $table->string('login_email');
            $table->string('password');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('event_id')->index('event_id');
                $table->string('name', 100);
                $table->string('email');
                $table->string('logo', 100);
                $table->string('booth', 100);
                $table->string('phone_number', 100)->nullable();
                $table->string('website');
                $table->string('twitter');
                $table->string('facebook');
                $table->string('linkedin');
                $table->tinyInteger('stype')->default(0)->index('stype')->comment('1= Gold, 2= Silver');
                $table->enum('allow_reservations', ['Y', 'N'])->default('N');
                $table->tinyInteger('status')->index('status');
                $table->tinyInteger('allow_card_reader')->default(0);
                $table->string('login_email');
                $table->string('password');
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
