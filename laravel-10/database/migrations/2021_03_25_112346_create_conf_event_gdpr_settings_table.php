<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventGdprSettingsTable extends Migration
    {

        const TABLE = 'conf_event_gdpr_settings';

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
                $table->tinyInteger('enable_gdpr')->default(1)->index('enable_gdpr');
                $table->tinyInteger('attendee_invisible')->default(1);
                $table->tinyInteger('gdpr_required')->default(0);
                $table->tinyInteger('auto_selected')->default(0);
                $table->text('bcc_emails');
                $table->timestamps();
            $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('event_id')->index('event_id');
                    $table->tinyInteger('enable_gdpr')->default(1)->index('enable_gdpr');
                    $table->tinyInteger('attendee_invisible')->default(1);
                    $table->tinyInteger('gdpr_required')->default(0);
                    $table->tinyInteger('auto_selected')->default(0);
                    $table->text('bcc_emails');
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
