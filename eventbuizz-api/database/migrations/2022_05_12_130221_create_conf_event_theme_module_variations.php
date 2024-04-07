<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventThemeModuleVariations extends Migration
{
    const TABLE = 'conf_event_theme_module_variations';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->foreignId("event_id")->index('event_id');
            $table->foreignId("theme_id")->index('theme_id');
            $table->string("alias");
            $table->string("module_name");
            $table->string("variation_name");
            $table->string("variation_slug");
            $table->enum("text_align", ['center', 'left'])->default('center');
            $table->string("background_image");
            $table->timestamps();
            $table->softDeletes();
        });
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->id();
                $table->foreignId("event_id");
                $table->foreignId("theme_id");
                $table->string("alias");
                $table->string("module_name");
                $table->string("variation_name");
                $table->string("variation_slug");
                $table->enum("text_align", ['center', 'left'])->default('center');
                $table->string("background_image");
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
