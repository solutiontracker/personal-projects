<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfAttendeeAuthenticationTable extends Migration
    {
        const TABLE = 'conf_attendee_authentication';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('email')->nullable();
                $table->string('token', 30)->nullable();
                $table->string('type', 30)->nullable();
                $table->string('to')->nullable();
                $table->string('refrer', 30)->nullable();
                $table->integer('event_id')->nullable();
                $table->timestamp('expire_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->string('email')->nullable();
                    $table->string('token', 30)->nullable();
                    $table->string('type', 30)->nullable();
                    $table->string('to')->nullable();
                    $table->string('refrer', 30)->nullable();
                    $table->integer('event_id')->nullable();
                    $table->timestamp('expire_at')->nullable();
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
