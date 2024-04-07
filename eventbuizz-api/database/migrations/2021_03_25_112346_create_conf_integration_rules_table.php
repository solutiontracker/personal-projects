<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfIntegrationRulesTable extends Migration
    {
        const TABLE = 'conf_integration_rules';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('integration_id')->nullable()->index('integration_id');
                $table->bigInteger('organizer_id')->nullable()->index('organizer_id');
                $table->string('name')->nullable();
                $table->string('value')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->unsignedBigInteger('integration_id')->nullable()->index('integration_id');
                    $table->bigInteger('organizer_id')->nullable()->index('organizer_id');
                    $table->string('name')->nullable();
                    $table->string('value')->nullable();
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
