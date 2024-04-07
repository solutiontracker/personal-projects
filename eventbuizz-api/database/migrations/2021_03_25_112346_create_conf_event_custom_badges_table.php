<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventCustomBadgesTable extends Migration
    {
        const TABLE = 'conf_event_custom_badges';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('organizer_id')->index('organizer_id');
                $table->string('name', 250);
                $table->tinyInteger('size')->default(0);
                $table->longText('body');
                $table->string('logo', 250);
                $table->string('background', 250);
                $table->string('badgefor')->nullable();
                $table->integer('badgeTypeId')->nullable()->index('badgeTypeId');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->bigInteger('organizer_id')->index('organizer_id');
                    $table->string('name', 250);
                    $table->tinyInteger('size')->default(0);
                    $table->longText('body');
                    $table->string('logo', 250);
                    $table->string('background', 250);
                    $table->string('badgefor')->nullable();
                    $table->integer('badgeTypeId')->nullable()->index('badgeTypeId');
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
