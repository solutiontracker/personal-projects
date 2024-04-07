<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddKeyTypeAndKeyTypeIdToConfConfOrganizerCalenderApiRequest extends Migration
{

    const TABLE = 'conf_organizer_calender_api_request';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('key_type');
            $table->unsignedBigInteger('key_type_id')->index('key_type_id');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('key_type');
                $table->unsignedBigInteger('key_type_id')->index('key_type_id');
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
            $table->dropColumn('key_type');
            $table->dropColumn('key_type_id');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('key_type');
                $table->dropColumn('key_type_id');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
