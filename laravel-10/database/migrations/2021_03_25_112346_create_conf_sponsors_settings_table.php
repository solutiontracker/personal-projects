<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfSponsorsSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_sponsors_settings';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('event_id')->index('event_id');
            $table->enum('sponsor_list', ['name', 'category'])->default('name');
            $table->tinyInteger('sponsorName')->default(0);
            $table->tinyInteger('sponsorPhone')->default(1);
            $table->tinyInteger('sponsorEmail')->default(1);
            $table->tinyInteger('contact_person_email')->default(0);
            $table->tinyInteger('contact_person_phone')->default(0);
            $table->tinyInteger('sponsorContact')->default(1);
            $table->tinyInteger('sponsorTab');
            $table->tinyInteger('catTab');
            $table->tinyInteger('sortType');
            $table->tinyInteger('hide_attendee');
            $table->tinyInteger('mark_favorite')->default(1);
            $table->tinyInteger('notes')->default(0);
            $table->tinyInteger('show_booth')->default(0);
            $table->integer('poll')->default(0);
            $table->integer('document')->default(0);
            $table->tinyInteger('reservation')->default(0);
            $table->tinyInteger('reservation_type')->default(0);
            $table->tinyInteger('reservation_req_type_email')->default(0);
            $table->tinyInteger('reservation_req_type_sms')->default(0);
            $table->tinyInteger('reservation_allow_contact_person')->default(1);
            $table->tinyInteger('reservation_allow_multiple')->default(0);
            $table->tinyInteger('allow_card_reader')->default(0);
            $table->integer('show_contact_person')->default(1);
            $table->integer('gdpr_accepted')->default(1);
            $table->integer('recieve_lead_email_on_save')->default(1);
            $table->tinyInteger('auto_save')->default(0);
            $table->text('bcc_emails')->nullable();
            $table->tinyInteger('show_lead_email_button')->default(1);
            $table->tinyInteger('enable_signature')->default(1);
            $table->tinyInteger('reservation_icone_view')->default(0);
            $table->tinyInteger('reservations_overview')->default(0);
            $table->tinyInteger('reservation_overview_icone')->default(0);
            $table->tinyInteger('reservations_view')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('event_id')->index('event_id');
                $table->enum('sponsor_list', ['name', 'category'])->default('name');
                $table->tinyInteger('sponsorName')->default(0);
                $table->tinyInteger('sponsorPhone')->default(1);
                $table->tinyInteger('sponsorEmail')->default(1);
                $table->tinyInteger('contact_person_email')->default(1);
                $table->tinyInteger('contact_person_phone')->default(0);
                $table->tinyInteger('sponsorContact')->default(1);
                $table->tinyInteger('sponsorTab');
                $table->tinyInteger('catTab');
                $table->tinyInteger('sortType');
                $table->tinyInteger('hide_attendee');
                $table->tinyInteger('mark_favorite')->default(1);
                $table->tinyInteger('notes')->default(0);
                $table->tinyInteger('show_booth')->default(0);
                $table->integer('poll')->default(0);
                $table->integer('document')->default(0);
                $table->tinyInteger('reservation')->default(0);
                $table->tinyInteger('reservation_type')->default(0);
                $table->tinyInteger('reservation_req_type_email')->default(0);
                $table->tinyInteger('reservation_req_type_sms')->default(0);
                $table->tinyInteger('reservation_allow_contact_person')->default(1);
                $table->tinyInteger('reservation_allow_multiple')->default(0);
                $table->tinyInteger('allow_card_reader')->default(0);
                $table->integer('show_contact_person')->default(1);
                $table->integer('gdpr_accepted')->default(1);
                $table->integer('recieve_lead_email_on_save')->default(1);
                $table->tinyInteger('auto_save')->default(0);
                $table->text('bcc_emails')->nullable();
                $table->tinyInteger('show_lead_email_button')->default(1);
                $table->tinyInteger('enable_signature')->default(1);
                $table->tinyInteger('reservation_icone_view')->default(0);
                $table->tinyInteger('reservations_overview')->default(0);
                $table->tinyInteger('reservation_overview_icone')->default(0);
                $table->tinyInteger('reservations_view')->default(0);
                $table->timestamps();
            $table->softDeletes();
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
