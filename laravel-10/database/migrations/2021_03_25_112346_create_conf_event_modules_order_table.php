<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventModulesOrderTable extends Migration
    {
        const TABLE = 'conf_event_modules_order';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('sort_order')->index('sort_order');
                $table->bigInteger('event_id')->index('event_id');
                $table->tinyInteger('status')->index('status');
                $table->timestamps();
            $table->softDeletes();
                $table->string('alias')->index('alias');
                $table->string('icon');
                $table->tinyInteger('is_purchased')->index('is_purchased');
                $table->enum('group', ['group1', 'group2', 'group3', 'group4', 'group5'])->index('group');
                $table->string('version');
                $table->enum('type', ['frontend', 'backend', 'backend_sub'])->default('backend')->index('type');
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('sort_order')->index('sort_order');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->tinyInteger('status')->index('status');
                    $table->timestamps();
            $table->softDeletes();
                    $table->string('alias')->index('alias');
                    $table->string('icon');
                    $table->tinyInteger('is_purchased')->index('is_purchased');
                    $table->enum('group', ['group1', 'group2', 'group3', 'group4', 'group5'])->index('group');
                    $table->string('version');
                    $table->enum('type', ['frontend', 'backend', 'backend_sub'])->default('backend')->index('type');
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
