<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_modules';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('alias')->index('alias');
            $table->string('class_name')->index('class_name');
            $table->enum('group', ['group1', 'group2', 'group3', 'group4', 'group5'])->index('group');
            $table->integer('sort_order');
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->string('version');
            $table->enum('type', ['frontend', 'backend', 'backend_sub'])->default('backend')->index('type');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->string('name');
                $table->string('alias')->index('alias');
                $table->string('class_name')->index('class_name');
                $table->enum('group', ['group1', 'group2', 'group3', 'group4', 'group5'])->index('group');
                $table->integer('sort_order');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            $table->softDeletes();
                $table->string('version');
                $table->enum('type', ['frontend', 'backend', 'backend_sub'])->default('backend')->index('type');
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
