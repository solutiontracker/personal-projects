<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventEmailTemplateLogTable extends Migration
    {
        const TABLE = 'conf_event_email_template_log';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->string('title');
                $table->string('subject');
                $table->text('template');
                $table->bigInteger('template_id')->index('template_id');
                $table->bigInteger('languages_id')->index('languages_id');
                $table->tinyInteger('status')->index('status');
                $table->timestamps();
            $table->softDeletes();
                $table->string('uuid', 150)->nullable();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->string('title');
                    $table->string('subject');
                    $table->text('template');
                    $table->bigInteger('template_id')->index('template_id');
                    $table->bigInteger('languages_id')->index('languages_id');
                    $table->tinyInteger('status')->index('status');
                    $table->timestamps();
            $table->softDeletes();
                    $table->string('uuid', 150)->nullable();
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
