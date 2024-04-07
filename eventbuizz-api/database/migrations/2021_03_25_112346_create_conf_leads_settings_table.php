<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfLeadsSettingsTable extends Migration
    {
        const TABLE = 'conf_leads_settings';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->bigInteger('event_id');
                $table->integer('recieve_lead_email_on_save')->default(1);
                $table->tinyInteger('allow_card_reader')->default(0);
                $table->tinyInteger('show_lead_email_button')->default(1);
                $table->tinyInteger('enable_signature')->default(1);
                $table->tinyInteger('lead_user_without_contact_person')->default(1);
                $table->tinyInteger('login_with_auth_code')->default(0);
                $table->tinyInteger('enable_organizer_approval')->default(0);
                $table->text('bcc_emails')->nullable();
                $table->string('access_code', 10);
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->bigInteger('event_id');
                    $table->integer('recieve_lead_email_on_save')->default(1);
                    $table->tinyInteger('allow_card_reader')->default(0);
                    $table->tinyInteger('show_lead_email_button')->default(1);
                    $table->tinyInteger('enable_signature')->default(1);
                    $table->tinyInteger('lead_user_without_contact_person')->default(1);
                    $table->tinyInteger('login_with_auth_code')->default(0);
                    $table->tinyInteger('enable_organizer_approval')->default(0);
                    $table->text('bcc_emails')->nullable();
                    $table->string('access_code', 10);
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
