<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfLayoutSections extends Migration
{
    const TABLE = 'conf_layout_sections';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->foreignId("layout_id")->index('layout_id');
            $table->string("variation_slug");
            $table->string("module_alias");
            $table->boolean("status");
            $table->bigInteger("sort_order");
            $table->timestamps();
            $table->softDeletes();
        });
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->id();
                $table->foreignId("layout_id");
                $table->string("variation_slug");
                $table->string("module_alias");
                $table->boolean("status");
                $table->bigInteger("sort_order");
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
