<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;


class AddRegistrationTypeToConfEventsiteSettings extends Migration
{
    const TABLE = 'conf_eventsite_settings';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

         Schema::table(self::TABLE, function (Blueprint $table) {
            $table->enum('registration_type', ['sponsor', 'exhibitor'])->index('registration_type')->nullable();
            $table->enum('portal_access', ['0', '1', '2'])->default('2')->index('portal_access');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->enum('registration_type', ['sponsor', 'exhibitor'])->index('registration_type')->nullable();
                $table->enum('portal_access', ['0', '1', '2'])->default('2')->index('portal_access');
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
            $table->dropColumn('registration_type');
            $table->dropColumn('portal_access');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('registration_type');
                $table->dropColumn('portal_access');
            });
            
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
