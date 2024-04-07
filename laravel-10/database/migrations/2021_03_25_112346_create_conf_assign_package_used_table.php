<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfAssignPackageUsedTable extends Migration
    {
        const TABLE = 'conf_assign_package_used';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->bigInteger('assign_package_id')->index('assign_package_id');
                $table->bigInteger('event_id')->index('event_id');
                $table->enum('is_expire', ['y', 'n'])->default('n')->index('is_expire');
                $table->dateTime('event_create_date');
                $table->dateTime('event_expire_date');
                $table->timestamps();
                $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->bigInteger('assign_package_id');
                    $table->bigInteger('event_id');
                    $table->enum('is_expire', ['y', 'n'])->default('n');
                    $table->dateTime('event_create_date');
                    $table->dateTime('event_expire_date');
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
