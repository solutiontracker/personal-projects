<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfFormBuilderFormsTable extends Migration
{
    const TABLE = 'conf_form_builder_forms';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->index();
            $table->foreignId('registration_form_id')->index()->default(0);
            $table->string('screenshot');
            $table->enum('status', ['published', 'draft']);
            $table->timestamps();
            $table->softDeletes();

        });
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->index();
                $table->foreignId('registration_form_id')->index()->default(0);
                $table->string('screenshot');
                $table->enum('status', ['published', 'draft']);
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
