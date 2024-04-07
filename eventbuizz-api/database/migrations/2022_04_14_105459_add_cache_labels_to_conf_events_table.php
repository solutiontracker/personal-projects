<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCacheLabelsToConfEventsTable extends Migration
{
    const TABLE = 'conf_events';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->boolean('cache_label')->default(1);
        });
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->boolean('cache_label')->default(1);
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
            $table->boolean('cache_label');
        });
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->boolean('cache_label');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}