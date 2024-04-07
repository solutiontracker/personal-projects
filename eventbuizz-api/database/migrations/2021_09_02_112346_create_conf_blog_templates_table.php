<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfBlogTemplatesTable extends Migration
{
    const TABLE = 'conf_blog_templates';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('organizer_id')->nullable();
            $table->string('name', 255)->nullable();
            $table->text('image')->nullable();
            $table->text('feature_image')->nullable();
            $table->longText('template')->nullable();
            $table->longText('content')->nullable();
            $table->string('exert')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->date('schedule_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('organizer_id')->nullable();
                $table->string('name', 255)->nullable();
                $table->text('image')->nullable();
                $table->text('feature_image')->nullable();
                $table->longText('template')->nullable();
                $table->longText('content')->nullable();
                $table->string('exert')->nullable();
                $table->tinyInteger('status')->nullable();
                $table->date('schedule_at')->nullable();
                $table->softDeletes();
                $table->timestamps();
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
