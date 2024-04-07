<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfCheckinUserTable extends Migration
    {
        const TABLE = 'conf_checkin_user';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('event_id')->index('event_id');
                $table->string('name');
                $table->string('email')->index('email');
                $table->string('password');
                $table->enum('status', ['1', '0'])->default('0')->index('status');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id', true);
                    $table->integer('event_id')->index('event_id');
                    $table->string('name');
                    $table->string('email')->index('email');
                    $table->string('password');
                    $table->enum('status', ['1', '0'])->default('0')->index('status');
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
