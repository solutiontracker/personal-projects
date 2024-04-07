<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventSubsectionsHeadingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_subsections_headings';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->string('track_heading');
            $table->string('group_heading');
            $table->string('category_heading');
            $table->string('exhibitor_category_heading');
            $table->bigInteger('event_id')->index('event_id');
            $table->tinyInteger('status')->index('status');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->string('track_heading');
                $table->string('group_heading');
                $table->string('category_heading');
                $table->string('exhibitor_category_heading');
                $table->bigInteger('event_id')->index('event_id');
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