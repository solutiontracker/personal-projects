<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddAllowBackgroundAndAllowTextAlignToConfThemeModuleVariations extends Migration
{
    const TABLE = 'conf_theme_module_variations';

     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->boolean("background_allowed")->default(1);
            $table->boolean("text_align_allowed")->default(1);
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->boolean("background_allowed")->default(1);
                $table->boolean("text_align_allowed")->default(1);
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
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('background_allowed');
            $table->dropColumn('text_align_allowed');
        });
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('background_allowed');
                $table->dropColumn('text_align_allowed');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
