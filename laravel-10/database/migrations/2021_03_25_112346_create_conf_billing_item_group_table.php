<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfBillingItemGroupTable extends Migration
    {
        const TABLE = 'conf_billing_item_group';

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
                $table->bigInteger('organizer_id')->index('organizer_id');
                $table->integer('status')->index('status');
                $table->enum('group_type', ['single', 'multiple'])->default('single')->index('group_type');
                $table->integer('sort_order');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->increments('id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->bigInteger('organizer_id')->index('organizer_id');
                    $table->integer('status')->index('status');
                    $table->enum('group_type', ['single', 'multiple'])->default('single')->index('group_type');
                    $table->integer('sort_order');
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
