<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventAgendasTable extends Migration
    {
        const TABLE = 'conf_event_agendas';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->bigInteger('event_id')->index('event_id');
                $table->date('start_date')->index('start_date');
                $table->time('start_time');
                $table->enum('link_type', ['', 'billing_item', 'subregistration', 'misc'])->default('')->index('link_type');
                $table->timestamps();
            $table->softDeletes();
                $table->bigInteger('workshop_id')->index('workshop_id');
                $table->tinyInteger('qa')->default(0)->index('qa');
                $table->integer('ticket')->default(0);
                $table->tinyInteger('enable_checkin')->default(0)->index('enable_checkin');
                $table->tinyInteger('enable_speakerlist')->default(0)->index('enable_speakerlist');
                $table->tinyInteger('hide_on_registrationsite')->index('hide_on_registrationsite');
                $table->tinyInteger('hide_on_app');
                $table->tinyInteger('only_for_qa');
                $table->tinyInteger('only_for_speaker_list');
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->date('start_date')->index('start_date');
                    $table->time('start_time');
                    $table->enum('link_type', ['', 'billing_item', 'subregistration', 'misc'])->default('')->index('link_type');
                    $table->timestamps();
            $table->softDeletes();
                    $table->bigInteger('workshop_id')->index('workshop_id');
                    $table->tinyInteger('qa')->default(0)->index('qa');
                    $table->integer('ticket')->default(0);
                    $table->tinyInteger('enable_checkin')->default(0)->index('enable_checkin');
                    $table->tinyInteger('enable_speakerlist')->default(0)->index('enable_speakerlist');
                    $table->tinyInteger('hide_on_registrationsite')->index('hide_on_registrationsite');
                    $table->tinyInteger('hide_on_app');
                    $table->tinyInteger('only_for_qa');
                    $table->tinyInteger('only_for_speaker_list');
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
