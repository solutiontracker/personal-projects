<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfTempAttendeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_temp_attendees';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->text('verification_id')->nullable();
            $table->bigInteger('organizer_id')->index('FK_ORGANIZER_ID');
            $table->bigInteger('event_id')->index('event_id');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email')->index('email');
            $table->string('delegate_number', 100);
            $table->string('table_number', 100);
            $table->string('password')->default('e10adc3949ba59abbe56e057f20f883e');
            $table->string('age', 250);
            $table->string('gender', 10);
            $table->string('image', 100)->default('no-img.jpg');
            $table->string('company_name');
            $table->string('title');
            $table->string('industry');
            $table->text('about');
            $table->string('phone', 100);
            $table->string('website');
            $table->string('facebook');
            $table->string('twitter');
            $table->string('linkedin');
            $table->string('linkedin_profile_id', 50);
            $table->string('fbprofile_id', 50);
            $table->text('fb_token')->nullable();
            $table->string('fb_url');
            $table->enum('registration_type', ['site', 'linkedin'])->default('site')->index('registration_type');
            $table->string('country');
            $table->string('organization');
            $table->string('jobs');
            $table->text('interests');
            $table->tinyInteger('allow_vote')->default(1)->comment('1=Yes, 0=No');
            $table->string('initial', 250);
            $table->string('department', 250);
            $table->bigInteger('custom_field_id');
            $table->string('network_group')->nullable();
            $table->tinyInteger('billing_ref_attendee');
            $table->string('billing_password', 250);
            $table->tinyInteger('isActivated')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->text('verification_id')->nullable();
                $table->bigInteger('organizer_id')->index('FK_ORGANIZER_ID');
                $table->bigInteger('event_id')->index('event_id');
                $table->string('first_name', 100);
                $table->string('last_name', 100);
                $table->string('email')->index('email');
                $table->string('delegate_number', 100);
                $table->string('table_number', 100);
                $table->string('password')->default('e10adc3949ba59abbe56e057f20f883e');
                $table->string('age', 250);
                $table->string('gender', 10);
                $table->string('image', 100)->default('no-img.jpg');
                $table->string('company_name');
                $table->string('title');
                $table->string('industry');
                $table->text('about');
                $table->string('phone', 100);
                $table->string('website');
                $table->string('facebook');
                $table->string('twitter');
                $table->string('linkedin');
                $table->string('linkedin_profile_id', 50);
                $table->string('fbprofile_id', 50);
                $table->text('fb_token')->nullable();
                $table->string('fb_url');
                $table->enum('registration_type', ['site', 'linkedin'])->default('site')->index('registration_type');
                $table->string('country');
                $table->string('organization');
                $table->string('jobs');
                $table->text('interests');
                $table->tinyInteger('allow_vote')->default(1)->comment('1=Yes, 0=No');
                $table->string('initial', 250);
                $table->string('department', 250);
                $table->bigInteger('custom_field_id');
                $table->string('network_group')->nullable();
                $table->tinyInteger('billing_ref_attendee');
                $table->string('billing_password', 250);
                $table->tinyInteger('isActivated')->nullable()->default(0);
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
