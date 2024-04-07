<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfAgoraCallDetailsTable extends Migration
    {
        const TABLE = 'conf_agora_call_details';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {

            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->bigInteger('call_id')->nullable()->index();
                $table->string('sdk_version')->nullable();
                $table->string('quit_state', 20)->nullable();
                $table->string('loc')->nullable();
                $table->bigInteger('attendee_id')->nullable()->index('attendee_id');
                $table->string('account')->nullable();
                $table->integer('join_ts')->nullable();
                $table->integer('leave_ts')->nullable();
                $table->string('ip', 20)->nullable();
                $table->integer('duration')->nullable()->default(0)->comment('in seconds');
                $table->timestamps();
                $table->softDeletes();
            });

            if (app()->environment('live')) {

                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id', true);
                    $table->bigInteger('call_id')->nullable();
                    $table->string('sdk_version')->nullable();
                    $table->string('quit_state', 20)->nullable();
                    $table->string('loc')->nullable();
                    $table->bigInteger('attendee_id')->nullable();
                    $table->string('account')->nullable();
                    $table->integer('join_ts')->nullable();
                    $table->integer('leave_ts')->nullable();
                    $table->string('ip', 20)->nullable();
                    $table->integer('duration')->nullable()->default(0)->comment('in seconds');
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
