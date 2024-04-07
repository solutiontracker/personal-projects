<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfGeneralInfoMenuInfoTable extends Migration
    {
        const TABLE = 'conf_general_info_menu_info';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->string('name');
                $table->string('value');
                $table->bigInteger('menu_id')->index('menu_id_2');
                $table->bigInteger('languages_id')->index('languages_id');
                $table->tinyInteger('status')->index('status');
                $table->timestamps();
            $table->softDeletes();
                $table->index(['menu_id', 'languages_id'], 'menu_id');
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->string('name');
                    $table->string('value');
                    $table->bigInteger('menu_id')->index('menu_id_2');
                    $table->bigInteger('languages_id')->index('languages_id');
                    $table->tinyInteger('status')->index('status');
                    $table->timestamps();
            $table->softDeletes();
                    $table->index(['menu_id', 'languages_id'], 'menu_id');
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
