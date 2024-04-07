<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventSiteTextTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_site_text';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('section_order')->index('section_order');
            $table->integer('constant_order')->index('constant_order');
            $table->string('alias')->index('alias');
            $table->string('module_alias')->index('module_alias');
            $table->bigInteger('event_id')->index('event_id');
            $table->bigInteger('parent_id')->index('parent_id');
            $table->integer('label_parent_id')->default(0)->index('label_parent_id');
            $table->tinyInteger('status')->index('status');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

	        Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
            $table->integer('section_order')->index('section_order');
            $table->integer('constant_order')->index('constant_order');
            $table->string('alias')->index('alias');
            $table->string('module_alias')->index('module_alias');
            $table->bigInteger('event_id')->index('event_id');
            $table->bigInteger('parent_id')->index('parent_id');
            $table->integer('label_parent_id')->default(0)->index('label_parent_id');
            $table->tinyInteger('status')->index('status');
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
