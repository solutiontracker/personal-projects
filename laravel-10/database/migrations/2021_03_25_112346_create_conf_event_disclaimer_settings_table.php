<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventDisclaimerSettingsTable extends Migration
    {
        const TABLE = 'conf_event_disclaimer_settings';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('event_id')->index('event_id');
                $table->tinyInteger('mobile_app')->default(1);
                $table->tinyInteger('reg_site')->default(1);
                $table->tinyInteger('reg_site_login')->nullable()->default(1);
                $table->timestamps();
            $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('event_id')->index('event_id');
                    $table->tinyInteger('mobile_app')->default(1);
                    $table->tinyInteger('reg_site')->default(1);
                    $table->tinyInteger('reg_site_login')->nullable()->default(1);
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
