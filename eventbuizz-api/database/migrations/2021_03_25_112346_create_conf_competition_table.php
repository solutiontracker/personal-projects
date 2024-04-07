<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfCompetitionTable extends Migration
    {
        const TABLE = 'conf_competition';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->bigInteger('organizer_id')->index('organizer_id');
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->string('from_company_name');
                $table->string('from_name');
                $table->string('title');
                $table->string('from_email');
                $table->string('from_phone', 100);
                $table->string('to_company_name');
                $table->string('to_name');
                $table->string('to_email');
                $table->string('to_phone', 100);
                $table->tinyInteger('status')->index('status');
                $table->timestamps();
            $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->bigInteger('organizer_id')->index('organizer_id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->bigInteger('attendee_id')->index('attendee_id');
                    $table->string('from_company_name');
                    $table->string('from_name');
                    $table->string('title');
                    $table->string('from_email');
                    $table->string('from_phone', 100);
                    $table->string('to_company_name');
                    $table->string('to_name');
                    $table->string('to_email');
                    $table->string('to_phone', 100);
                    $table->tinyInteger('status')->index('status');
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
