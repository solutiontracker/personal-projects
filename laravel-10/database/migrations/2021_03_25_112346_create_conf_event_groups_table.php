<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventGroupsTable extends Migration
    {
        const TABLE = 'conf_event_groups';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->bigInteger('parent_id')->index('parent_id');
                $table->enum('link_type', ['', 'billing_item']);
                $table->bigInteger('event_id')->index('event_id');
                $table->string('color', 10);
                $table->bigInteger('sort_order');
                $table->tinyInteger('allow_multiple')->default(0);
                $table->tinyInteger('status')->default(1)->index('status');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->bigInteger('parent_id')->index('parent_id');
                    $table->enum('link_type', ['', 'billing_item']);
                    $table->bigInteger('event_id')->index('event_id');
                    $table->string('color', 10);
                    $table->bigInteger('sort_order');
                    $table->tinyInteger('allow_multiple')->default(0);
                    $table->tinyInteger('status')->default(1)->index('status');
                    $table->timestamps();
            $table->softDeletes();
                });
                EBSchema::createBeforeDeleteTrigger(self::TABLE);#
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