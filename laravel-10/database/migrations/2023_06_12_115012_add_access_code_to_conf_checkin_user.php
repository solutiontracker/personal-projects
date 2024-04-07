<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccessCodeToConfCheckinUser extends Migration
{
    const TABLE = 'conf_checkin_user';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table(self::TABLE, function (Blueprint $table) {
            $table->integer("access_code")->nullable();
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->integer("access_code")->nullable();
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
            $table->dropColumn('access_code');
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('access_code');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
