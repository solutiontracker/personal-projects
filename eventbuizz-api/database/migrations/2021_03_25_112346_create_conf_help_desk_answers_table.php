<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfHelpDeskAnswersTable extends Migration
    {
        const TABLE = 'conf_help_desk_answers';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->text('answer')->nullable();
                $table->bigInteger('sender_id')->nullable()->index('sender_id');
                $table->bigInteger('help_desk_id')->nullable()->index('qa_id');
                $table->tinyInteger('is_admin')->nullable()->index('is_admin');
                $table->timestamps();
                $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->text('answer')->nullable();
                    $table->bigInteger('sender_id')->nullable()->index('sender_id');
                    $table->bigInteger('help_desk_id')->nullable()->index('qa_id');
                    $table->tinyInteger('is_admin')->nullable()->index('is_admin');
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
