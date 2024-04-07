<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttendeeIdToConfStandSaleRegistrationLinks extends Migration
{
    const TABLE = "conf_stand_sale_registration_links";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table(self::TABLE, function (Blueprint $table) {
            $table->integer('attendee_id')->index('attendee_id')->nullable();

        });
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
            $table->integer('attendee_id')->index('attendee_id')->nullable();
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
            $table->dropColumn('attendee_id');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('attendee_id');
            });
            
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
