<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfHelpDeskTable extends Migration
    {
        const TABLE = 'conf_help_desk';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('answered');
                $table->tinyInteger('show_projector')->index('show_projector');
                $table->tinyInteger('rejected');
                $table->dateTime('q_startTime');
                $table->tinyInteger('isStart');
                $table->tinyInteger('displayed')->index('displayed');
                $table->tinyInteger('sort_order');
                $table->unsignedBigInteger('attendee_id')->index('attendee_id');
                $table->unsignedBigInteger('event_id')->index('event_id');
                $table->bigInteger('group_id')->index('group_id');
                $table->tinyInteger('anonymous_user')->nullable()->default(0);
                $table->integer('like_count');
                $table->timestamps();
                $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->string('answered');
                    $table->tinyInteger('show_projector')->index('show_projector');
                    $table->tinyInteger('rejected');
                    $table->dateTime('q_startTime');
                    $table->tinyInteger('isStart');
                    $table->tinyInteger('displayed')->index('displayed');
                    $table->tinyInteger('sort_order');
                    $table->unsignedBigInteger('attendee_id')->index('attendee_id');
                    $table->unsignedBigInteger('event_id')->index('event_id');
                    $table->bigInteger('group_id')->index('group_id');
                    $table->tinyInteger('anonymous_user')->nullable()->default(0);
                    $table->integer('like_count');
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
