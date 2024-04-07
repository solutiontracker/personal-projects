<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventHotelsTable extends Migration
    {
        const TABLE = 'conf_event_hotels';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->bigInteger('event_id')->index('Event Id');
                $table->string('name');
                $table->integer('rooms');
                $table->double('price', 11, 2);
                $table->double('vat', 11, 2)->default(0);
                $table->enum('price_type', ['fixed', 'notfixed'])->default('notfixed');
                $table->integer('max_rooms');
                $table->date('hotel_from_date')->nullable();
                $table->date('hotel_to_date')->nullable();
                $table->bigInteger('sort_order');
                $table->integer('status')->index('Status');
                $table->integer('is_archive');
                $table->tinyInteger('new_imp_flag')->default(1);
                $table->timestamps();
                $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->bigInteger('event_id')->index('Event Id');
                    $table->string('name');
                    $table->integer('rooms');
                    $table->double('price', 11, 2);
                    $table->double('vat', 11, 2)->default(0);
                    $table->enum('price_type', ['fixed', 'notfixed'])->default('notfixed');
                    $table->integer('max_rooms');
                    $table->date('hotel_from_date')->nullable();
                    $table->date('hotel_to_date')->nullable();
                    $table->bigInteger('sort_order');
                    $table->integer('status')->index('Status');
                    $table->integer('is_archive');
                    $table->tinyInteger('new_imp_flag')->default(1);
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
