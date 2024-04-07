<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfAdditionalInfoPagesInfoTable extends Migration
    {
        const TABLE = 'conf_additional_info_pages_info';

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
                $table->text('value');
                $table->bigInteger('page_id')->index('page_id');
                $table->bigInteger('languages_id')->index('languages_id');
                $table->tinyInteger('status')->index('status');
                $table->timestamps();
                $table->softDeletes();
            });
            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->string('name');
                    $table->text('value');
                    $table->bigInteger('page_id');
                    $table->bigInteger('languages_id');
                    $table->tinyInteger('status');
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
