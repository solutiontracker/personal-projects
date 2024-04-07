<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventsiteSectionsInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_eventsite_sections_info';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('name')->index('name');
            $table->string('value');
            $table->bigInteger('languages_id')->index('languages_id');
            $table->tinyInteger('status')->index('status');
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('section_id')->index('section_id');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->string('name')->index('name');
                $table->string('value');
                $table->bigInteger('languages_id')->index('languages_id');
                $table->tinyInteger('status')->index('status');
                $table->timestamps();
            $table->softDeletes();
                $table->bigInteger('section_id')->index('section_id');
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