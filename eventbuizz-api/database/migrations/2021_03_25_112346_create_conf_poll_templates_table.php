<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfPollTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_poll_templates';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('event_id')->index('event_id');
            $table->string('name')->nullable();
            $table->string('position');
            $table->string('preview_image');
            $table->tinyInteger('status')->index('status');
            $table->tinyInteger('sort_order');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('event_id')->index('event_id');
                $table->string('name')->nullable();
                $table->string('position');
                $table->string('preview_image');
                $table->tinyInteger('status')->index('status');
                $table->tinyInteger('sort_order');
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
