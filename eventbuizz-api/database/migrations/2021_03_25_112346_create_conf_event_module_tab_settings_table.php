<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventModuleTabSettingsTable extends Migration
    {
        const TABLE = 'conf_event_module_tab_settings';

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
                $table->string('tab_name', 111);
                $table->string('module', 111);
                $table->tinyInteger('sort_order');
                $table->tinyInteger('status')->default(1)->index('status');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('event_id')->index('event_id');
                    $table->string('tab_name', 111);
                    $table->string('module', 111);
                    $table->tinyInteger('sort_order');
                    $table->tinyInteger('status')->default(1)->index('status');
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
