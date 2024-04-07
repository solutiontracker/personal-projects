<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventInfoPagesTable extends Migration
    {
        const TABLE = 'conf_event_info_pages';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->bigInteger('sort_order');
                $table->bigInteger('menu_id')->index('menu_id');
                $table->bigInteger('event_id')->index('event_id');
                $table->tinyInteger('page_type')->comment('1=cms page; 2=url');
                $table->string('image', 100);
                $table->string('image_position', 100);
                $table->string('pdf', 200);
                $table->string('icon', 100);
                $table->string('url');
                $table->string('website_protocol', 10);
                $table->tinyInteger('status')->index('status');
                $table->timestamps();
            $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->bigInteger('sort_order');
                    $table->bigInteger('menu_id')->index('menu_id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->tinyInteger('page_type')->comment('1=cms page; 2=url');
                    $table->string('image', 100);
                    $table->string('image_position', 100);
                    $table->string('pdf', 200);
                    $table->string('icon', 100);
                    $table->string('url');
                    $table->string('website_protocol', 10);
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
