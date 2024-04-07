<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventGroupInfoTable extends Migration
    {
        const TABLE = 'conf_event_group_info';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->string('name', 250);
                $table->string('value', 250);
                $table->dateTime('end_date');
                $table->bigInteger('languages_id')->index('languages_id');
                $table->bigInteger('group_id')->index('group_id');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->
                create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->string('name', 250);
                    $table->string('value', 250);
                    $table->dateTime('end_date');
                    $table->bigInteger('languages_id')->index('languages_id');
                    $table->bigInteger('group_id')->index('group_id');
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
