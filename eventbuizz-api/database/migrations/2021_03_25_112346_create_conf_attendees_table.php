<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfAttendeesTable extends Migration
    {
        const TABLE = 'conf_attendees';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->string('email')->index('email');
                $table->string('ss_number')->index('ss_number');
                $table->string('password');
                $table->string('first_name');
                $table->string('last_name');
                $table->bigInteger('organizer_id')->index('organizer_id');
                $table->string('FIRST_NAME_PASSPORT')->nullable();
                $table->string('LAST_NAME_PASSPORT')->nullable();
                $table->dateTime('BIRTHDAY_YEAR')->nullable();
                $table->date('EMPLOYMENT_DATE')->nullable();
                $table->string('SPOKEN_LANGUAGE')->nullable();
                $table->string('image');
                $table->tinyInteger('status')->default(1)->index('status');
                $table->enum('show_home', ['1', '0'])->default('0')->index('show_home');
                $table->tinyInteger('allow_vote')->default(1)->comment('1=Yes, 0=No');
                $table->tinyInteger('billing_ref_attendee');
                $table->string('billing_password', 250);
                $table->tinyInteger('change_password')->default(1)->comment('1=default password; 0=password changed');
                $table->string('phone', 55)->nullable();
                $table->tinyInteger('is_updated')->default(1)->index('is_updated');
                $table->tinyInteger('is_deleted')->nullable()->default(0);
                $table->string('pid');
                $table->dateTime('pid_date')->nullable();
                $table->string('remember_token')->nullable();
                $table->tinyInteger('type_resource_bk')->default(0)->index('type_resource');
                $table->timestamps();
            $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->string('email')->index('email');
                    $table->string('ss_number')->index('ss_number');
                    $table->string('password');
                    $table->string('first_name');
                    $table->string('last_name');
                    $table->bigInteger('organizer_id')->index('organizer_id');
                    $table->string('FIRST_NAME_PASSPORT')->nullable();
                    $table->string('LAST_NAME_PASSPORT')->nullable();
                    $table->dateTime('BIRTHDAY_YEAR')->nullable();
                    $table->date('EMPLOYMENT_DATE')->nullable();
                    $table->string('SPOKEN_LANGUAGE')->nullable();
                    $table->string('image');
                    $table->tinyInteger('status')->default(1)->index('status');
                    $table->enum('show_home', ['1', '0'])->default('0')->index('show_home');
                    $table->tinyInteger('allow_vote')->default(1)->comment('1=Yes, 0=No');
                    $table->tinyInteger('billing_ref_attendee');
                    $table->string('billing_password', 250);
                    $table->tinyInteger('change_password')->default(1)->comment('1=default password; 0=password changed');
                    $table->string('phone', 55)->nullable();
                    $table->tinyInteger('is_updated')->default(1)->index('is_updated');
                    $table->tinyInteger('is_deleted')->nullable()->default(0);
                    $table->string('pid');
                    $table->dateTime('pid_date')->nullable();
                    $table->string('remember_token')->nullable();
                    $table->tinyInteger('type_resource_bk')->default(0)->index('type_resource');
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
