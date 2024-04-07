<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageBuilderPagesTable extends Migration
{
    const TABLE = 'conf_page_builder_pages';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('name');
            $table->integer('page_id');
            $table->integer('event_id')->nullable();
            $table->text('assets');
            $table->text('components');
            $table->longText('css');
            $table->longText('html');
            $table->text('styles');
            $table->tinyInteger('status')->index('status');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->string('name');
                $table->integer('page_id');
                $table->integer('event_id')->nullable();
                $table->text('assets');
                $table->text('components');
                $table->longText('css');
                $table->longText('html');
                $table->text('styles');
                $table->tinyInteger('status')->index('status');
                $table->timestamps();
                $table->softDeletes();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        EBSchema::dropDeleteTrigger(self::TABLE);
        Schema::dropIfExists(self::TABLE);
        Schema::connection(config('database.archive_connection'))->dropIfExists(self::TABLE);
    }
}
