<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfLoginHistoryTable extends Migration
    {
        const TABLE = 'conf_login_history';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('attendee_id')->index('attendee_id');
                $table->integer('event_id')->index('event_id');
                $table->enum('platform', ['webapp', 'registration_site', 'ios', 'android', 'desktop_app'])->index('platform');
                $table->string('ip', 250)->index('ip');
                $table->string('user_agent', 250);
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->
                create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('attendee_id')->index('attendee_id');
                    $table->integer('event_id')->index('event_id');
                    $table->enum('platform', ['webapp', 'registration_site', 'ios', 'android', 'desktop_app'])->index('platform');
                    $table->string('ip', 250)->index('ip');
                    $table->string('user_agent', 250);
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