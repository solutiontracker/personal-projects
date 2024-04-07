<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfAnalyticsRequestsTable extends Migration
    {
        const TABLE = 'conf_analytics_requests';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('event_code')->index('event_code');
                $table->string('event_name', 254);
                $table->integer('organizer_id')->index('organizer_id');
                $table->string('organizer_name', 111);
                $table->string('analytics_email', 254);
                $table->string('analytics_code', 254);
                $table->string('profile_id', 111);
                $table->tinyInteger('status')->default(0)->index('status');
                $table->timestamps();
                $table->softDeletes();
            });

            if (app()->environment('live')) {

                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('event_code');
                    $table->string('event_name', 254);
                    $table->integer('organizer_id');
                    $table->string('organizer_name', 111);
                    $table->string('analytics_email', 254);
                    $table->string('analytics_code', 254);
                    $table->string('profile_id', 111);
                    $table->tinyInteger('status')->default(0);
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
