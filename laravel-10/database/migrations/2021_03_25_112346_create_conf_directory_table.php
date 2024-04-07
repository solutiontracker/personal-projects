<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfDirectoryTable extends Migration
    {
        const TABLE = 'conf_directory';

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
                $table->bigInteger('other')->index('other');
                $table->bigInteger('agenda_id')->index('agenda_id');
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('speaker_id')->index('speaker_id');
                $table->bigInteger('sponsor_id')->index('sponsor_id');
                $table->bigInteger('exhibitor_id')->index('exhibitor_id');
                $table->tinyInteger('sort_order');
                $table->timestamps();
            $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->bigInteger('parent_id')->index('parent_id');
                    $table->bigInteger('other')->index('other');
                    $table->bigInteger('agenda_id')->index('agenda_id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->bigInteger('speaker_id')->index('speaker_id');
                    $table->bigInteger('sponsor_id')->index('sponsor_id');
                    $table->bigInteger('exhibitor_id')->index('exhibitor_id');
                    $table->tinyInteger('sort_order');
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
