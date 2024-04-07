<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfDirectoryFilesTable extends Migration
    {
        const TABLE = 'conf_directory_files';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->bigInteger('organizer_id')->index('organizer_id');
                $table->bigInteger('directory_id')->index('directory_id');
                $table->bigInteger('parent_id')->index('parent_id');
                $table->bigInteger('file_size');
                $table->string('path');
                $table->date('start_date')->index('start_date');
                $table->time('start_time')->index('start_time');
                $table->tinyInteger('sort_order');
                $table->tinyInteger('s3')->nullable()->default(0)->index('s3');
                $table->timestamps();
            $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->bigInteger('organizer_id')->index('organizer_id');
                    $table->bigInteger('directory_id')->index('directory_id');
                    $table->bigInteger('parent_id')->index('parent_id');
                    $table->bigInteger('file_size');
                    $table->string('path');
                    $table->date('start_date')->index('start_date');
                    $table->time('start_time')->index('start_time');
                    $table->tinyInteger('sort_order');
                    $table->tinyInteger('s3')->nullable()->default(0)->index('s3');
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
