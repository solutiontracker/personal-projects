<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfLabelsTable extends Migration
    {
        const TABLE = 'conf_labels';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('section_order')->index('section_order');
                $table->integer('constant_order')->index('constant_order');
                $table->string('alias')->index('alias');
                $table->string('module_alias')->index('module_alias');
                $table->bigInteger('parent_id')->index('parent_id');
                $table->tinyInteger('status')->index('status');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->increments('id');
                    $table->integer('section_order')->index('section_order');
                    $table->integer('constant_order')->index('constant_order');
                    $table->string('alias')->index('alias');
                    $table->string('module_alias')->index('module_alias');
                    $table->bigInteger('parent_id')->index('parent_id');
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
