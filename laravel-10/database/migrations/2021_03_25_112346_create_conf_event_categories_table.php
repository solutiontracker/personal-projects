<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventCategoriesTable extends Migration
    {
        const TABLE = 'conf_event_categories';

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
                $table->bigInteger('parent_id')->index('parent_id');
                $table->string('color', 10);
                $table->bigInteger('sort_order');
                $table->tinyInteger('status')->index('status');
                $table->enum('cat_type', ['sponsors', 'exhibitors', 'speakers'])->default('sponsors')->index('cat_type');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->bigInteger('parent_id')->index('parent_id');
                    $table->string('color', 10);
                    $table->bigInteger('sort_order');
                    $table->tinyInteger('status')->index('status');
                    $table->enum('cat_type', ['sponsors', 'exhibitors', 'speakers'])->default('sponsors')->index('cat_type');
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
