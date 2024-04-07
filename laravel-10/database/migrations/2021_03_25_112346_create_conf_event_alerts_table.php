<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventAlertsTable extends Migration
    {
        const TABLE = 'conf_event_alerts';

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
                $table->tinyInteger('pre_schedule')->default(0)->index('pre_schedule')->comment('0=no; 1=yes');
                $table->date('alert_date')->index('alert_date');
                $table->time('alert_time')->index('alert_time');
                $table->enum('sendto', ['all', 'agendas', 'groups', 'individuals', 'workshops', 'polls', 'surveys', 'sponsors', 'exhibitors', 'attendee_type'])->index('sendto');
                $table->tinyInteger('alert_email')->default(0)->index('alert_email')->comment('0=no; 1=yes');
                $table->tinyInteger('alert_sms')->default(0)->index('alert_sms')->comment('0=no; 1=yes');
                $table->tinyInteger('status')->index('status')->comment('1=PENDING,  2=SENT,');
                $table->timestamps();
            $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->tinyInteger('pre_schedule')->default(0)->index('pre_schedule')->comment('0=no; 1=yes');
                    $table->date('alert_date')->index('alert_date');
                    $table->time('alert_time')->index('alert_time');
                    $table->enum('sendto', ['all', 'agendas', 'groups', 'individuals', 'workshops', 'polls', 'surveys', 'sponsors', 'exhibitors', 'attendee_type'])->index('sendto');
                    $table->tinyInteger('alert_email')->default(0)->index('alert_email')->comment('0=no; 1=yes');
                    $table->tinyInteger('alert_sms')->default(0)->index('alert_sms')->comment('0=no; 1=yes');
                    $table->tinyInteger('status')->index('status')->comment('1=PENDING,  2=SENT,');
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
