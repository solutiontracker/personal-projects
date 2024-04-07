<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventAttendeesTable extends Migration
    {
        const TABLE = 'conf_event_attendees';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->tinyInteger('email_sent')->index('email_sent');
                $table->tinyInteger('sms_sent')->index('sms_sent');
                $table->tinyInteger('login_yet')->index('login_yet');
                $table->tinyInteger('status')->index('status');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->bigInteger('event_id')->index('event_id');
                $table->enum('speaker', ['1', '0'])->default('0')->index('speaker')->comment('1==Yes, 0==No');
                $table->enum('sponser', ['1', '0'])->default('0')->index('sponser')->comment('1==Yes, 0==No');
                $table->enum('exhibitor', ['1', '0'])->default('0')->index('exhibitor')->comment('1==Yes, 0==No');
                $table->integer('attendee_type')->default(0)->index('attendee_type');
                $table->integer('default_language_id')->default(1)->index('default_language_id');
                $table->string('device_token');
                $table->enum('device_type', ['ios', 'android', 'windows'])->index('device_type');
                $table->tinyInteger('app_invite_sent')->default(0)->index('app_invite_sent');
                $table->tinyInteger('is_active')->default(1)->index('is_active');
                $table->string('verification_id')->index('verification_id');
                $table->tinyInteger('gdpr')->default(0)->index('gdpr');
                $table->tinyInteger('allow_vote')->nullable()->default(0)->index('allow_vote');
                $table->tinyInteger('allow_gallery')->nullable()->default(0)->index('allow_gallery');
                $table->tinyInteger('ask_to_apeak')->nullable()->default(0)->index('ask_to_apeak');
                $table->tinyInteger('type_resource')->nullable()->default(0)->index('type_resource');
                $table->tinyInteger('allow_my_document')->nullable()->default(0)->index('allow_my_document');
                $table->tinyInteger('camera')->nullable()->default(0);
                $table->tinyInteger('accept_foods_allergies')->default(0)->index('accept_foods_allergies');
                $table->string('native_app_forgot_password_code')->default('0');
                $table->timestamp('native_app_forgot_password_code_created_at')->default('0000-00-00 00:00:00');
                $table->integer('attendee_share_value')->default(0);
                $table->timestamps();
            $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->tinyInteger('email_sent')->index('email_sent');
                    $table->tinyInteger('sms_sent')->index('sms_sent');
                    $table->tinyInteger('login_yet')->index('login_yet');
                    $table->tinyInteger('status')->index('status');
                    $table->bigInteger('attendee_id')->index('attendee_id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->enum('speaker', ['1', '0'])->default('0')->index('speaker')->comment('1==Yes, 0==No');
                    $table->enum('sponser', ['1', '0'])->default('0')->index('sponser')->comment('1==Yes, 0==No');
                    $table->enum('exhibitor', ['1', '0'])->default('0')->index('exhibitor')->comment('1==Yes, 0==No');
                    $table->integer('attendee_type')->default(0)->index('attendee_type');
                    $table->integer('default_language_id')->default(1)->index('default_language_id');
                    $table->string('device_token');
                    $table->enum('device_type', ['ios', 'android', 'windows'])->index('device_type');
                    $table->tinyInteger('app_invite_sent')->default(0)->index('app_invite_sent');
                    $table->tinyInteger('is_active')->default(1)->index('is_active');
                    $table->string('verification_id')->index('verification_id');
                    $table->tinyInteger('gdpr')->default(0)->index('gdpr');
                    $table->tinyInteger('allow_vote')->nullable()->default(0)->index('allow_vote');
                    $table->tinyInteger('allow_gallery')->nullable()->default(0)->index('allow_gallery');
                    $table->tinyInteger('ask_to_apeak')->nullable()->default(0)->index('ask_to_apeak');
                    $table->tinyInteger('type_resource')->nullable()->default(0)->index('type_resource');
                    $table->tinyInteger('allow_my_document')->nullable()->default(0)->index('allow_my_document');
                    $table->tinyInteger('camera')->nullable()->default(0);
                    $table->tinyInteger('accept_foods_allergies')->default(0)->index('accept_foods_allergies');
                    $table->string('native_app_forgot_password_code')->default('0');
                    $table->timestamp('native_app_forgot_password_code_created_at')->default('0000-00-00 00:00:00');
                    $table->integer('attendee_share_value')->default(0);
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
