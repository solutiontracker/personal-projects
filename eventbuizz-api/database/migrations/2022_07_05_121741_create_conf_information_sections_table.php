<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfInformationSectionsTable extends Migration
{
    const TABLE = 'conf_information_sections';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->string('alias');
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status');
            $table->softDeletes();
            $table->timestamps();
        });
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id');
                $table->string('alias');
                $table->string('icon')->nullable();
                $table->integer('sort_order')->default(0);
                $table->tinyInteger('status');
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
