<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfLogImportTable extends Migration
    {
        const TABLE = 'conf_log_import';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->bigInteger('parent_organizer_id')->index('parent_organizer_id');
                $table->bigInteger('organizer_id')->index('organizer_id');
                $table->bigInteger('event_id')->index('event_id');
                $table->string('import_entity', 55)->index('import_entity');
                $table->string('file_name', 55);
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id', true);
                    $table->bigInteger('parent_organizer_id')->index('parent_organizer_id');
                    $table->bigInteger('organizer_id')->index('organizer_id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->string('import_entity', 55)->index('import_entity');
                    $table->string('file_name', 55);
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
