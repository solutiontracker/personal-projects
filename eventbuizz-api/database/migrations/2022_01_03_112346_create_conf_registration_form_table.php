<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfRegistrationFormTable extends Migration
{
    const TABLE = 'conf_registration_form';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->bigInteger('type_id')->default(0)->nullable();
            $table->bigInteger('event_id')->index()->default(0)->nullable();
            $table->tinyInteger('is_default')->default(0)->nullable();
            $table->tinyInteger('status')->default(1)->nullable();
            $table->string('color', 20)->default('#f78700')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->bigInteger('type_id')->default(0)->nullable();
                $table->bigInteger('event_id')->index()->default(0)->nullable();
                $table->tinyInteger('is_default')->default(0)->nullable();
                $table->tinyInteger('status')->default(1)->nullable();
                $table->string('color', 20)->default('#f78700')->nullable();
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
