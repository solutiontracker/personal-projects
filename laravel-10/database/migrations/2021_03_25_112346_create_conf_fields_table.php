<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfFieldsTable extends Migration
    {
        const TABLE = 'conf_fields';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('sort_order');
                $table->tinyInteger('status');
                $table->tinyInteger('mandatory')->index('mandatory');
                $table->string('field_alias');
                $table->enum('type', ['section', 'field', '', '']);
                $table->string('section_alias');
                $table->timestamps();
                $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('sort_order');
                    $table->tinyInteger('status');
                    $table->tinyInteger('mandatory')->index('mandatory');
                    $table->string('field_alias');
                    $table->enum('type', ['section', 'field', '', '']);
                    $table->string('section_alias');
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
