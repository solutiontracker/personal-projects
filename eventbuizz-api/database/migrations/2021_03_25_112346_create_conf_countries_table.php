<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfCountriesTable extends Migration
    {

        const TABLE = 'conf_countries';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->char('code_2', 2)->nullable()->index('code_2');
                $table->string('name', 80)->default('');
                $table->integer('languages_id')->default(1)->index('languages_id');
                $table->string('language');
                $table->string('language_name');
                $table->integer('parent_id')->index('parent_id');
                $table->string('full_name', 80)->default('');
                $table->char('code_1', 3)->nullable()->index('code_1');
                $table->string('numcode', 6)->nullable();
                $table->string('un_member', 12)->nullable();
                $table->integer('calling_code')->nullable();
                $table->string('cctld', 5)->nullable();
                $table->string('alias');
                $table->string('lat');
                $table->string('lon');
                $table->string('gmt');
                $table->timestamps();
            $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->char('code_2', 2)->nullable()->index('code_2');
                    $table->string('name', 80)->default('');
                    $table->integer('languages_id')->default(1)->index('languages_id');
                    $table->string('language');
                    $table->string('language_name');
                    $table->integer('parent_id')->index('parent_id');
                    $table->string('full_name', 80)->default('');
                    $table->char('code_1', 3)->nullable()->index('code_1');
                    $table->string('numcode', 6)->nullable();
                    $table->string('un_member', 12)->nullable();
                    $table->integer('calling_code')->nullable();
                    $table->string('cctld', 5)->nullable();
                    $table->string('alias');
                    $table->string('lat');
                    $table->string('lon');
                    $table->string('gmt');
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
