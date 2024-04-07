<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfAssignAdditionalFeatureTable extends Migration
    {
        const TABLE = 'conf_assign_additional_feature';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('organizer_id')->index('organizer_id');
                $table->enum('name', ['Internal Organizer', 'Allow business card', 'Event calendar API key', 'Email marketing template', 'Mailing list', 'Eventbuizz native app', 'White label native app', 'Allow NEM Id', 'Access plug n play'])->index('name');
                $table->enum('alias', ['internal_organizer', 'allow_card_reader', 'allow_api', 'email_marketing_template', 'mailing_list', 'eventbuizz_app', 'white_label_app', 'allow_nem_id', 'allow_plug_and_play_access'])->index('alias');
                $table->tinyInteger('status')->default(1)->index('status');
                $table->timestamps();
            $table->softDeletes();
                $table->timestamp('licence_start_date')->default('0000-00-00 00:00:00');
                $table->timestamp('licence_end_date')->default('0000-00-00 00:00:00');
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('organizer_id');
                    $table->enum('name', ['Internal Organizer', 'Allow business card', 'Event calendar API key', 'Email marketing template', 'Mailing list', 'Eventbuizz native app', 'White label native app', 'Allow NEM Id', 'Access plug n play']);
                    $table->enum('alias', ['internal_organizer', 'allow_card_reader', 'allow_api', 'email_marketing_template', 'mailing_list', 'eventbuizz_app', 'white_label_app', 'allow_nem_id', 'allow_plug_and_play_access']);
                    $table->tinyInteger('status')->default(1);
                    $table->timestamps();
                    $table->softDeletes();
                    $table->timestamp('licence_start_date')->default('0000-00-00 00:00:00');
                    $table->timestamp('licence_end_date')->default('0000-00-00 00:00:00');
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
