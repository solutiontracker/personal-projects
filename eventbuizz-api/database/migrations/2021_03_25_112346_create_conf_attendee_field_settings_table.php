<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfAttendeeFieldSettingsTable extends Migration
{
    const TABLE = 'conf_attendee_field_settings';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('event_id')->index('event_id');
            $table->tinyInteger('initial')->default(1);
            $table->tinyInteger('first_name')->default(1);
            $table->tinyInteger('last_name')->default(1);
            $table->tinyInteger('email')->default(1);
            $table->tinyInteger('password')->default(1);
            $table->tinyInteger('phone_number')->default(1);
            $table->tinyInteger('age')->default(1);
            $table->tinyInteger('gender')->default(1);
            $table->tinyInteger('first_name_passport')->default(1);
            $table->tinyInteger('last_name_passport')->default(1);
            $table->tinyInteger('place_of_birth')->default(0);
            $table->tinyInteger('passport_no')->default(0);
            $table->tinyInteger('date_of_issue_passport')->default(0);
            $table->tinyInteger('date_of_expiry_passport')->default(0);
            $table->tinyInteger('birth_date')->default(1);
            $table->tinyInteger('spoken_languages')->default(1);
            $table->tinyInteger('profile_picture')->default(1);
            $table->tinyInteger('website')->default(1);
            $table->tinyInteger('linkedin')->default(1);
            $table->tinyInteger('facebook')->default(1);
            $table->tinyInteger('twitter')->default(1);
            $table->tinyInteger('company_name')->default(1);
            $table->tinyInteger('title')->default(1);
            $table->tinyInteger('department')->default(1);
            $table->tinyInteger('organization')->default(1);
            $table->tinyInteger('employment_date')->default(1);
            $table->tinyInteger('custom_field')->default(1);
            $table->tinyInteger('country')->default(1);
            $table->tinyInteger('industry')->default(1);
            $table->tinyInteger('job_tasks')->default(1);
            $table->tinyInteger('interests')->default(1);
            $table->tinyInteger('about')->default(1);
            $table->tinyInteger('network_group')->default(1);
            $table->tinyInteger('delegate_number')->default(1);
            $table->tinyInteger('table_number')->default(1);
            $table->tinyInteger('event_language')->default(1);
            $table->tinyInteger('pa_house_no')->default(0);
            $table->tinyInteger('pa_street')->default(0);
            $table->tinyInteger('pa_post_code')->default(0);
            $table->tinyInteger('pa_city')->default(0);
            $table->tinyInteger('pa_country')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('event_id')->index('event_id');
                $table->tinyInteger('initial')->default(1);
                $table->tinyInteger('first_name')->default(1);
                $table->tinyInteger('last_name')->default(1);
                $table->tinyInteger('email')->default(1);
                $table->tinyInteger('password')->default(1);
                $table->tinyInteger('phone_number')->default(1);
                $table->tinyInteger('age')->default(1);
                $table->tinyInteger('gender')->default(1);
                $table->tinyInteger('first_name_passport')->default(1);
                $table->tinyInteger('last_name_passport')->default(1);
                $table->tinyInteger('place_of_birth')->default(0);
                $table->tinyInteger('passport_no')->default(0);
                $table->tinyInteger('date_of_issue_passport')->default(0);
                $table->tinyInteger('date_of_expiry_passport')->default(0);
                $table->tinyInteger('birth_date')->default(1);
                $table->tinyInteger('spoken_languages')->default(1);
                $table->tinyInteger('profile_picture')->default(1);
                $table->tinyInteger('website')->default(1);
                $table->tinyInteger('linkedin')->default(1);
                $table->tinyInteger('facebook')->default(1);
                $table->tinyInteger('twitter')->default(1);
                $table->tinyInteger('company_name')->default(1);
                $table->tinyInteger('title')->default(1);
                $table->tinyInteger('department')->default(1);
                $table->tinyInteger('organization')->default(1);
                $table->tinyInteger('employment_date')->default(1);
                $table->tinyInteger('custom_field')->default(1);
                $table->tinyInteger('country')->default(1);
                $table->tinyInteger('industry')->default(1);
                $table->tinyInteger('job_tasks')->default(1);
                $table->tinyInteger('interests')->default(1);
                $table->tinyInteger('about')->default(1);
                $table->tinyInteger('network_group')->default(1);
                $table->tinyInteger('delegate_number')->default(1);
                $table->tinyInteger('table_number')->default(1);
                $table->tinyInteger('event_language')->default(1);
                $table->tinyInteger('pa_house_no')->default(0);
                $table->tinyInteger('pa_street')->default(0);
                $table->tinyInteger('pa_post_code')->default(0);
                $table->tinyInteger('pa_city')->default(0);
                $table->tinyInteger('pa_country')->default(0);
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
