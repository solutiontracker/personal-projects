<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventDataLogTable extends Migration
    {
        const TABLE = 'conf_event_data_log';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->string('login_time');
                $table->string('action_mod');
                $table->string('action_func');
                $table->string('action_detail_id')->index('action_detail_id');
                $table->string('param_1');
                $table->string('param_all');
                $table->string('log_time');
                $table->string('ip_address');
                $table->string('device_type');
                $table->string('operating_system');
                $table->string('browser_type');
                $table->string('browser_version');
                $table->string('user_agent');
                $table->string('referel_url');
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->string('login_time');
                    $table->string('action_mod');
                    $table->string('action_func');
                    $table->string('action_detail_id')->index('action_detail_id');
                    $table->string('param_1');
                    $table->string('param_all');
                    $table->string('log_time');
                    $table->string('ip_address');
                    $table->string('device_type');
                    $table->string('operating_system');
                    $table->string('browser_type');
                    $table->string('browser_version');
                    $table->string('user_agent');
                    $table->string('referel_url');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->bigInteger('attendee_id')->index('attendee_id');
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
