<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateFloorPlanPinsTable extends Migration
{
    const TABLE = "conf_floor_plan_pins";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->string('coordinateX');
            $table->string('coordinateY');
            $table->enum("type", ["sponsor", "exhibitor"]);
            $table->unsignedBigInteger("type_id");
            $table->unsignedBigInteger("floor_plan_id");
            $table->timestamps();

            $table->index("type_id");
            $table->index("floor_plan_id");
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->id();
                $table->string('coordinateX');
                $table->string('coordinateY');
                $table->enum("type", ["sponsor", "exhibitor"]);
                $table->unsignedBigInteger("type_id");
                $table->unsignedBigInteger("floor_plan_id");
                $table->timestamps();

                $table->index("type_id");
                $table->index("floor_plan_id");
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
        Schema::dropIfExists(self::TABLE);
        EBSchema::dropDeleteTrigger(self::TABLE);
        Schema::connection(config('database.archive_connection'))->dropIfExists(self::TABLE);
    }
}
