<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfHelpDeskInfoTable extends Migration
    {
        const TABLE = 'conf_help_desk_info';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->text('value');
                $table->bigInteger('help_desk_id')->index('help_desk_id');
                $table->bigInteger('languages_id')->index('languages_id');
                $table->tinyInteger('status')->nullable()->index('status');
                $table->timestamps();
                $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->string('name');
                    $table->text('value');
                    $table->bigInteger('help_desk_id')->index('help_desk_id');
                    $table->bigInteger('languages_id')->index('languages_id');
                    $table->tinyInteger('status')->nullable()->index('status');
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
