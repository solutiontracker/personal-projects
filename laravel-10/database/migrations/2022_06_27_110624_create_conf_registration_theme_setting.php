<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfRegistrationThemeSetting extends Migration
{
    const TABLE = 'conf_registration_theme_setting';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->enum('mode', ['dark', 'light'])->nullable();
            $table->string('body_color', 20)->nullable();
            $table->string('wrapper_color', 20)->nullable();
            $table->bigInteger('event_id')->index('event_id');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->id();
                $table->enum('mode', ['dark', 'light'])->nullable();
                $table->string('body_color', 20)->nullable();
                $table->string('wrapper_color', 20)->nullable();
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
