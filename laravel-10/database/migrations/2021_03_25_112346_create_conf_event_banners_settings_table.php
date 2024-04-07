<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventBannersSettingsTable extends Migration
    {
        const TABLE = 'conf_event_banners_settings';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->bigInteger('event_id')->index('event_id');
                $table->string('main_banner_position', 55)->default('bottom')->index('main_banner_position');
                $table->enum('native_banner_position', ['one', 'two'])->default('one');
                $table->tinyInteger('bannerads_orderby')->default(0);
                $table->tinyInteger('display_banner')->default(1);
                $table->timestamps();
                $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->string('main_banner_position', 55)->default('bottom')->index('main_banner_position');
                    $table->enum('native_banner_position', ['one', 'two'])->default('one');
                    $table->tinyInteger('bannerads_orderby')->default(0);
                    $table->tinyInteger('display_banner')->default(1);
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
