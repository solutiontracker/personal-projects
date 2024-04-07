<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddMultipleColumnsToConfNotifications extends Migration
{
    const TABLE = 'conf_notifications';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->unsignedBigInteger('model_id')->nullable()->index('model_id');
            $table->string('model_name')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->unsignedBigInteger('model_id')->nullable()->index('model_id');
                $table->string('model_name')->nullable();
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
            $table->dropColumn('model_id');
            $table->dropColumn('model_name');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('model_id');
                $table->dropColumn('model_name');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
