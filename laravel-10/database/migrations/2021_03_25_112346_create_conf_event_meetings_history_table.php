<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventMeetingsHistoryTable extends Migration
    {
        const TABLE = 'conf_event_meetings_history';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->bigInteger('event_id')->nullable()->index('event_id');
                $table->bigInteger('attendee_id')->nullable()->index('attendee_id');
                $table->enum('plateform', ['agora'])->nullable();
                $table->string('channel')->nullable();
                $table->tinyInteger('audio')->nullable()->default(1);
                $table->tinyInteger('video')->nullable()->default(1);
                $table->tinyInteger('share')->nullable()->default(1);
                $table->timestamps();
                $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->bigInteger('event_id')->nullable()->index('event_id');
                    $table->bigInteger('attendee_id')->nullable()->index('attendee_id');
                    $table->enum('plateform', ['agora'])->nullable();
                    $table->string('channel')->nullable();
                    $table->tinyInteger('audio')->nullable()->default(1);
                    $table->tinyInteger('video')->nullable()->default(1);
                    $table->tinyInteger('share')->nullable()->default(1);
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
