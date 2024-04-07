<?php
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegistrationFormIdEventHotelTable extends Migration
{
    const TABLE = 'conf_event_hotels';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('registration_form_id')->nullable()->default(0)->index('registration_form_id');
            $table->string('url', 255)->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('registration_form_id')->nullable()->default(0)->index('registration_form_id');
                $table->string('url', 255)->nullable();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('registration_form_id');
            $table->dropColumn('url');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('registration_form_id');
                $table->dropColumn('url');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
