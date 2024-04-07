<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfCronPushNotificationTable extends Migration
    {
        const TABLE = 'conf_cron_push_notification';

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
                $table->integer('event_id')->index('event_id');
                $table->string('deviceType')->index('deviceType');
                $table->string('deviceToken');
                $table->bigInteger('alert_id')->index('alert_id');
                $table->date('alert_date');
                $table->time('alert_time');
                $table->string('alertTtile');
                $table->text('alertDescription');
                $table->integer('badge_count');
                $table->enum('status', ['2', '1', '0'])->default('0')->index('status');
                $table->text('responce');
                $table->timestamps();
            $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('organizer_id')->index('organizer_id');
                    $table->integer('event_id')->index('event_id');
                    $table->string('deviceType')->index('deviceType');
                    $table->string('deviceToken');
                    $table->bigInteger('alert_id')->index('alert_id');
                    $table->date('alert_date');
                    $table->time('alert_time');
                    $table->string('alertTtile');
                    $table->text('alertDescription');
                    $table->integer('badge_count');
                    $table->enum('status', ['2', '1', '0'])->default('0')->index('status');
                    $table->text('responce');
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
