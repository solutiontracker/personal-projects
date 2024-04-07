<?php
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileImageColumnConfEventUserLeadsTable extends Migration
{
    const TABLE = 'conf_event_user_leads';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('profile_image_data')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('profile_image_data')->nullable();
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('profile_image_data');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('profile_image_data');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
