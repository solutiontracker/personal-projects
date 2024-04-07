<?php
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttendeeTypeBillingOrderAttendeesTable extends Migration
{
    const TABLE = 'conf_billing_order_attendees';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('attendee_type')->nullable()->index('attendee_type');
            $table->integer('registration_form_id')->nullable()->index('registration_form_id');
            $table->enum('status', ['incomplete', 'complete'])->index('status')->nullable();
            $table->string('subscriber_ids')->nullable();
            $table->tinyInteger('accept_foods_allergies')->default(0)->nullable();
            $table->tinyInteger('accept_gdpr')->default(0)->nullable();
            $table->tinyInteger('cbkterms')->default(0)->nullable();
            $table->tinyInteger('member_number')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('attendee_type')->nullable()->index('attendee_type');
                $table->integer('registration_form_id')->nullable()->index('registration_form_id');
                $table->enum('status', ['incomplete', 'complete'])->index('status')->nullable();
                $table->string('subscriber_ids')->nullable();
                $table->tinyInteger('accept_foods_allergies')->default(0)->nullable();
                $table->tinyInteger('accept_gdpr')->default(0)->nullable();
                $table->tinyInteger('cbkterms')->default(0)->nullable();
                $table->tinyInteger('member_number')->nullable();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('attendee_type');
            $table->dropColumn('registration_form_id');
            $table->dropColumn('status');
            $table->dropColumn('subscriber_ids');
            $table->dropColumn('accept_foods_allergies');
            $table->dropColumn('accept_gdpr');
            $table->dropColumn('cbkterms');
            $table->dropColumn('member_number');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('attendee_type');
                $table->dropColumn('registration_form_id');
                $table->dropColumn('status');
                $table->dropColumn('subscriber_ids');
                $table->dropColumn('accept_foods_allergies');
                $table->dropColumn('accept_gdpr');
                $table->dropColumn('cbkterms');
                $table->dropColumn('member_number');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
