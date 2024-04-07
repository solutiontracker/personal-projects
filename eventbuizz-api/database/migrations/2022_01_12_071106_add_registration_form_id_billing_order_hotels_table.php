<?php
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRegistrationFormIdBillingOrderHotelsTable extends Migration
{
    const TABLE = 'conf_event_order_hotels';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('registration_form_id')->nullable()->index('registration_form_id');
            $table->bigInteger('attendee_id')->nullable()->index('attendee_id');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('registration_form_id')->nullable()->index('registration_form_id');
                $table->bigInteger('attendee_id')->nullable()->index('attendee_id');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('registration_form_id');
            $table->dropColumn('attendee_id');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('registration_form_id');
                $table->dropColumn('attendee_id');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
