<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventsiteSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_eventsite_settings';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('event_id')->index('event_id');
            $table->string('ticket_left', 250);
            $table->dateTime('registration_end_date')->default('0000-00-00 00:00:00');
            $table->time('registration_end_time')->default('23:59:00');
            $table->dateTime('cancellation_date')->default('0000-00-00 00:00:00');
            $table->time('cancellation_end_time')->default('23:59:00');
            $table->longText('cancellation_policy');
            $table->string('registration_code', 100);
            $table->string('mobile_phone', 100);
            $table->tinyInteger('eventsite_public')->default(0);
            $table->tinyInteger('eventsite_signup_linkedin')->default(1);
            $table->integer('eventsite_signup_fb')->default(1);
            $table->tinyInteger('eventsite_tickets_left')->default(1);
            $table->tinyInteger('eventsite_time_left')->default(1);
            $table->tinyInteger('eventsite_language_menu')->default(0);
            $table->tinyInteger('eventsite_menu')->default(1);
            $table->tinyInteger('eventsite_banners')->default(1);
            $table->tinyInteger('eventsite_location')->default(1);
            $table->tinyInteger('eventsite_date')->default(1);
            $table->tinyInteger('eventsite_footer')->default(1);
            $table->tinyInteger('pass_changeable')->default(0);
            $table->tinyInteger('phone_mandatory')->default(0);
            $table->tinyInteger('attendee_registration_invite_email')->default(1);
            $table->tinyInteger('attach_attendee_ticket')->default(1);
            $table->tinyInteger('attendee_my_profile')->default(1);
            $table->tinyInteger('attendee_my_program')->default(1);
            $table->tinyInteger('attendee_my_billing')->default(1);
            $table->tinyInteger('attendee_my_billing_history')->default(1);
            $table->tinyInteger('attendee_my_reg_cancel')->default(1);
            $table->tinyInteger('attendee_my_sub_registration')->default(1);
            $table->tinyInteger('third_party_redirect')->default(0);
            $table->tinyInteger('agenda_search_filter')->default(0);
            $table->text('third_party_redirect_url')->nullable();
            $table->tinyInteger('attach_my_program')->default(0);
            $table->tinyInteger('quick_register')->default(0);
            $table->tinyInteger('prefill_reg_form')->default(0);
            $table->tinyInteger('attendee_go_to_mbl_app')->default(0);
            $table->tinyInteger('payment_type')->default(0);
            $table->integer('use_waitinglist')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->tinyInteger('goto_eventsite')->default(0);
            $table->tinyInteger('eventsite_add_calender')->default(1);
            $table->tinyInteger('registration_after_login')->default(0);
            $table->tinyInteger('send_invoice_email')->default(1);
            $table->integer('attach_invoice_email')->default(1);
            $table->tinyInteger('attach_calendar_to_email')->default(0);
            $table->tinyInteger('auto_complete')->default(0);
            $table->tinyInteger('new_message_temp')->default(0);
            $table->tinyInteger('search_engine_visibility')->default(0);
            $table->tinyInteger('attach_invoice_email_online_payment')->nullable()->default(0);
            $table->tinyInteger('go_to_account')->default(1);
            $table->tinyInteger('go_to_home_page')->default(1);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('event_id')->index('event_id');
                $table->string('ticket_left', 250);
                $table->dateTime('registration_end_date')->default('0000-00-00 00:00:00');
                $table->time('registration_end_time')->default('23:59:00');
                $table->dateTime('cancellation_date')->default('0000-00-00 00:00:00');
                $table->time('cancellation_end_time')->default('23:59:00');
                $table->longText('cancellation_policy');
                $table->string('registration_code', 100);
                $table->string('mobile_phone', 100);
                $table->tinyInteger('eventsite_public')->default(0);
                $table->tinyInteger('eventsite_signup_linkedin')->default(1);
                $table->integer('eventsite_signup_fb')->default(1);
                $table->tinyInteger('eventsite_tickets_left')->default(1);
                $table->tinyInteger('eventsite_time_left')->default(1);
                $table->tinyInteger('eventsite_language_menu')->default(0);
                $table->tinyInteger('eventsite_menu')->default(1);
                $table->tinyInteger('eventsite_banners')->default(1);
                $table->tinyInteger('eventsite_location')->default(1);
                $table->tinyInteger('eventsite_date')->default(1);
                $table->tinyInteger('eventsite_footer')->default(1);
                $table->tinyInteger('pass_changeable')->default(0);
                $table->tinyInteger('phone_mandatory')->default(0);
                $table->tinyInteger('attendee_registration_invite_email')->default(1);
                $table->tinyInteger('attach_attendee_ticket')->default(1);
                $table->tinyInteger('attendee_my_profile')->default(1);
                $table->tinyInteger('attendee_my_program')->default(1);
                $table->tinyInteger('attendee_my_billing')->default(1);
                $table->tinyInteger('attendee_my_billing_history')->default(1);
                $table->tinyInteger('attendee_my_reg_cancel')->default(1);
                $table->tinyInteger('attendee_my_sub_registration')->default(1);
                $table->tinyInteger('third_party_redirect')->default(0);
                $table->tinyInteger('agenda_search_filter')->default(0);
                $table->text('third_party_redirect_url')->nullable();
                $table->tinyInteger('attach_my_program')->default(0);
                $table->tinyInteger('quick_register')->default(0);
                $table->tinyInteger('prefill_reg_form')->default(0);
                $table->tinyInteger('attendee_go_to_mbl_app')->default(0);
                $table->tinyInteger('payment_type')->default(0);
                $table->integer('use_waitinglist')->default(0);
                $table->timestamps();
            $table->softDeletes();
                $table->tinyInteger('goto_eventsite')->default(0);
                $table->tinyInteger('eventsite_add_calender')->default(1);
                $table->tinyInteger('registration_after_login')->default(1);
                $table->tinyInteger('send_invoice_email')->default(1);
                $table->integer('attach_invoice_email')->default(1);
                $table->tinyInteger('attach_calendar_to_email')->default(0);
                $table->tinyInteger('auto_complete')->default(0);
                $table->tinyInteger('new_message_temp')->default(0);
                $table->tinyInteger('search_engine_visibility')->default(0);
                $table->tinyInteger('attach_invoice_email_online_payment')->nullable()->default(0);
                $table->tinyInteger('go_to_account')->default(1);
                $table->tinyInteger('go_to_home_page')->default(1);
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
        EBSchema::dropDeleteTrigger(self::TABLE);
        Schema::dropIfExists(self::TABLE);
            Schema::connection(config('database.archive_connection'))->dropIfExists(self::TABLE);
    }
}
