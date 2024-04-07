<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfGalleryInfoTable extends Migration
    {
        const TABLE = 'conf_gallery_info';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('value');
                $table->bigInteger('image_id')->index('image_id');
                $table->bigInteger('languages_id')->index('languages_id');
                $table->tinyInteger('status')->index('status');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->
                create(self::TABLE, function (Blueprint $table) {
                    $table->increments('id');
                    $table->string('name');
                    $table->string('value');
                    $table->bigInteger('image_id')->index('image_id');
                    $table->bigInteger('languages_id')->index('languages_id');
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