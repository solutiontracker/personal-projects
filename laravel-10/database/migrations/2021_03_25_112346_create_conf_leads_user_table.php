<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfLeadsUserTable extends Migration
    {
        const TABLE = 'conf_leads_user';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('event_id');
                $table->string('name', 254);
                $table->string('email', 254);
                $table->string('phone');
                $table->string('password', 254);
                $table->enum('status', ['1', '0'])->default('0');
                $table->tinyInteger('verified')->default(0);
                $table->tinyInteger('approved');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('event_id');
                    $table->string('name', 254);
                    $table->string('email', 254);
                    $table->string('phone');
                    $table->string('password', 254);
                    $table->enum('status', ['1', '0'])->default('0');
                    $table->tinyInteger('verified')->default(0);
                    $table->tinyInteger('approved');
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
