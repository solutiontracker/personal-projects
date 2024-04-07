<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventDataLogSessionTable extends Migration
    {
        const TABLE = 'conf_event_data_log_session';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->string('session_id')->index('session_id');
                $table->string('session_expires');
                $table->string('session_data');
                $table->string('delete_test');
                $table->string('login_time');
                $table->string('login_update');
                $table->string('logout_time');
                $table->string('ip_address');
                $table->string('operating_system');
                $table->string('device_type');
                $table->string('browser_type');
                $table->string('browser_version');
                $table->string('user_agent');
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->string('session_id')->index('session_id');
                    $table->string('session_expires');
                    $table->string('session_data');
                    $table->string('delete_test');
                    $table->string('login_time');
                    $table->string('login_update');
                    $table->string('logout_time');
                    $table->string('ip_address');
                    $table->string('operating_system');
                    $table->string('device_type');
                    $table->string('browser_type');
                    $table->string('browser_version');
                    $table->string('user_agent');
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
