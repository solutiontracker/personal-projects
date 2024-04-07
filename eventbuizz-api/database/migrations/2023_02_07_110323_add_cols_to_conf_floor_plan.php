<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddColsToConfFloorPlan extends Migration
{
    const TABLE = "conf_floor_plan";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string("floor_plan_name")->default(null);
            $table->string("version_number")->default(null);
            $table->string("area_floor")->default(null);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string("floor_plan_name")->default(null);
                $table->string("version_number")->default(null);
                $table->string("area_floor")->default(null);
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
            $table->dropColumn(["floor_plan_name", "floor_plan_name", "area_floor"]);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn(["floor_plan_name", "floor_plan_name", "area_floor"]);
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
