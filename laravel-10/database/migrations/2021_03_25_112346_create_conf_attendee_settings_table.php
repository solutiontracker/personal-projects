<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfAttendeeSettingsTable extends Migration
    {
        const TABLE = 'conf_attendee_settings';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('domain_names', 500)->nullable();
                $table->bigInteger('event_id')->index('event_id');
                $table->tinyInteger('phone')->default(1);
                $table->tinyInteger('email')->default(1);
                $table->tinyInteger('title')->default(1);
                $table->tinyInteger('organization')->default(1);
                $table->tinyInteger('department')->default(1);
                $table->tinyInteger('company_name')->default(1);
                $table->tinyInteger('show_country')->default(0);
                $table->tinyInteger('contact_vcf')->default(1);
                $table->tinyInteger('linkedin')->default(1);
                $table->tinyInteger('linkedin_registration')->default(0);
                $table->tinyInteger('registration_password')->default(0);
                $table->tinyInteger('program')->default(1);
                $table->tinyInteger('attendee_group')->default(1);
                $table->tinyInteger('attendee_my_group')->nullable()->default(0);
                $table->tinyInteger('tab')->default(1);
                $table->tinyInteger('initial')->default(0);
                $table->tinyInteger('network_group')->default(1);
                $table->tinyInteger('table_number')->default(1);
                $table->tinyInteger('delegate_number')->default(1);
                $table->tinyInteger('voting')->default(1);
                $table->tinyInteger('allow_my_document')->default(1);
                $table->tinyInteger('image_gallery')->default(1);
                $table->enum('default_display', ['name', 'group'])->default('name');
                $table->tinyInteger('create_profile')->default(1);
                $table->string('default_password')->default('123456');
                $table->tinyInteger('facebook_enable')->default(0);
                $table->tinyInteger('hide_password')->default(0);
                $table->tinyInteger('default_password_label')->default(0);
                $table->tinyInteger('forgot_link')->default(0);
                $table->timestamps();
            $table->softDeletes();
                $table->tinyInteger('attendee_reg_verification')->default(0);
                $table->tinyInteger('validate_attendee_invite')->default(0);
                $table->tinyInteger('interest')->default(1);
                $table->tinyInteger('show_custom_field')->default(0);
                $table->tinyInteger('bio_info')->default(1);
                $table->tinyInteger('show_job_tasks')->default(1);
                $table->tinyInteger('show_industry')->default(1);
                $table->tinyInteger('password_lenght')->default(0);
                $table->tinyInteger('strong_password')->default(0);
                $table->tinyInteger('enable_foods')->default(0);
                $table->tinyInteger('authentication')->nullable()->default(1);
                $table->tinyInteger('cpr')->default(0);
                $table->tinyInteger('place_of_birth')->default(0);
                $table->tinyInteger('passport_no')->default(0);
                $table->tinyInteger('date_of_issue_passport')->default(0);
                $table->tinyInteger('date_of_expiry_passport')->default(0);
                $table->tinyInteger('pa_house_no')->default(0);
                $table->tinyInteger('pa_street')->default(0);
                $table->tinyInteger('pa_post_code')->default(0);
                $table->tinyInteger('pa_city')->default(0);
                $table->tinyInteger('pa_country')->default(0);
                $table->tinyInteger('display_private_address')->default(0);
                $table->tinyInteger('email_enable')->default(1);
                $table->tinyInteger('share_enable')->nullable()->default(0);
                $table->tinyInteger('share_validation_enable')->nullable()->default(0);
                $table->integer('share_value')->nullable()->default(0);
                $table->tinyInteger('display_chat_notification')->default(1);
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->string('domain_names', 500)->nullable();
                    $table->bigInteger('event_id')->index('event_id');
                    $table->tinyInteger('phone')->default(1);
                    $table->tinyInteger('email')->default(1);
                    $table->tinyInteger('title')->default(1);
                    $table->tinyInteger('organization')->default(1);
                    $table->tinyInteger('department')->default(1);
                    $table->tinyInteger('company_name')->default(1);
                    $table->tinyInteger('show_country')->default(0);
                    $table->tinyInteger('contact_vcf')->default(1);
                    $table->tinyInteger('linkedin')->default(1);
                    $table->tinyInteger('linkedin_registration')->default(0);
                    $table->tinyInteger('registration_password')->default(0);
                    $table->tinyInteger('program')->default(1);
                    $table->tinyInteger('attendee_group')->default(1);
                    $table->tinyInteger('attendee_my_group')->nullable()->default(0);
                    $table->tinyInteger('tab')->default(1);
                    $table->tinyInteger('initial')->default(0);
                    $table->tinyInteger('network_group')->default(1);
                    $table->tinyInteger('table_number')->default(1);
                    $table->tinyInteger('delegate_number')->default(1);
                    $table->tinyInteger('voting')->default(1);
                    $table->tinyInteger('allow_my_document')->default(1);
                    $table->tinyInteger('image_gallery')->default(1);
                    $table->enum('default_display', ['name', 'group'])->default('name');
                    $table->tinyInteger('create_profile')->default(1);
                    $table->string('default_password')->default('123456');
                    $table->tinyInteger('facebook_enable')->default(0);
                    $table->tinyInteger('hide_password')->default(0);
                    $table->tinyInteger('default_password_label')->default(0);
                    $table->tinyInteger('forgot_link')->default(0);
                    $table->timestamps();
            $table->softDeletes();
                    $table->tinyInteger('attendee_reg_verification')->default(0);
                    $table->tinyInteger('validate_attendee_invite')->default(0);
                    $table->tinyInteger('interest')->default(1);
                    $table->tinyInteger('show_custom_field')->default(0);
                    $table->tinyInteger('bio_info')->default(1);
                    $table->tinyInteger('show_job_tasks')->default(1);
                    $table->tinyInteger('show_industry')->default(1);
                    $table->tinyInteger('password_lenght')->default(0);
                    $table->tinyInteger('strong_password')->default(0);
                    $table->tinyInteger('enable_foods')->default(0);
                    $table->tinyInteger('authentication')->nullable()->default(1);
                    $table->tinyInteger('cpr')->default(0);
                    $table->tinyInteger('place_of_birth')->default(0);
                    $table->tinyInteger('passport_no')->default(0);
                    $table->tinyInteger('date_of_issue_passport')->default(0);
                    $table->tinyInteger('date_of_expiry_passport')->default(0);
                    $table->tinyInteger('pa_house_no')->default(0);
                    $table->tinyInteger('pa_street')->default(0);
                    $table->tinyInteger('pa_post_code')->default(0);
                    $table->tinyInteger('pa_city')->default(0);
                    $table->tinyInteger('pa_country')->default(0);
                    $table->tinyInteger('display_private_address')->default(0);
                    $table->tinyInteger('email_enable')->default(1);
                    $table->tinyInteger('share_enable')->nullable()->default(0);
                    $table->tinyInteger('share_validation_enable')->nullable()->default(0);
                    $table->integer('share_value')->nullable()->default(0);
                    $table->tinyInteger('display_chat_notification')->default(1);
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
