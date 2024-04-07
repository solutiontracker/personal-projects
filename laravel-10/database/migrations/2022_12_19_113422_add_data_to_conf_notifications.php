<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataToConfNotifications extends Migration
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
            $table->longText('data')->nullable()->default('');
        });
        \DB::statement("ALTER TABLE `conf_notifications` CHANGE `type` `type` ENUM('login-request','organizer-alert','message-alert') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'login-request';");
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->longText('data')->nullable()->default('');
            });
            \DB::statement("ALTER TABLE `conf_notifications` CHANGE `type` `type` ENUM('login-request','organizer-alert','message-alert') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'login-request';");

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
            $table->dropColumn('data');
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('data');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
