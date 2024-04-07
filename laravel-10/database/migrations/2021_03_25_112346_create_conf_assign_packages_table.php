<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfAssignPackagesTable extends Migration
    {
        const TABLE = 'conf_assign_packages';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('admin_id')->index('admin_id');
                $table->integer('organizer_id')->index('organizer_id');
                $table->integer('package_id')->index('package_id');
                $table->string('no_of_event');
                $table->integer('expire_duration');
                $table->integer('total_attendees');
                $table->dateTime('package_assign_date');
                $table->dateTime('package_expire_date');
                $table->timestamps();
            $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('admin_id');
                    $table->integer('organizer_id');
                    $table->integer('package_id');
                    $table->string('no_of_event');
                    $table->integer('expire_duration');
                    $table->integer('total_attendees');
                    $table->dateTime('package_assign_date');
                    $table->dateTime('package_expire_date');
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
