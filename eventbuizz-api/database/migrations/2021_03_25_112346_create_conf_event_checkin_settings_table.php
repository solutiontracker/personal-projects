<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventCheckinSettingsTable extends Migration
    {
        const TABLE = 'conf_event_checkin_settings';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->bigInteger('event_id')->index('event_id');
                $table->tinyInteger('status')->default(0)->comment('0=disable, 1= enable');
                $table->enum('type', ['single', 'multiple'])->nullable();
                $table->enum('single_type', ['per_event', 'per_day'])->nullable();
                $table->string('radius', 15)->nullable();
                $table->string('latitude', 15)->nullable()->default('0');
                $table->string('longitude', 15)->nullable()->default('0');
                $table->string('address', 1500)->nullable();
                $table->tinyInteger('gps_checkin')->default(0);
                $table->tinyInteger('self_checkin')->default(0);
                $table->tinyInteger('event_checkin')->default(1);
                $table->tinyInteger('program_checkin')->default(0);
                $table->tinyInteger('group_checkin')->default(0);
                $table->tinyInteger('ticket_checkin')->default(0);
                $table->tinyInteger('validate_program_checkin')->default(0);
                $table->tinyInteger('show_wp')->default(1);
                $table->tinyInteger('show_vp')->default(1);
                $table->tinyInteger('show_qrcode')->default(1);
                $table->tinyInteger('enable_email_ticket')->default(1);
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->tinyInteger('status')->default(0)->comment('0=disable, 1= enable');
                    $table->enum('type', ['single', 'multiple'])->nullable();
                    $table->enum('single_type', ['per_event', 'per_day'])->nullable();
                    $table->string('radius', 15)->nullable();
                    $table->string('latitude', 15)->nullable()->default('0');
                    $table->string('longitude', 15)->nullable()->default('0');
                    $table->string('address', 1500)->nullable();
                    $table->tinyInteger('gps_checkin')->default(0);
                    $table->tinyInteger('self_checkin')->default(0);
                    $table->tinyInteger('event_checkin')->default(1);
                    $table->tinyInteger('program_checkin')->default(0);
                    $table->tinyInteger('group_checkin')->default(0);
                    $table->tinyInteger('ticket_checkin')->default(0);
                    $table->tinyInteger('validate_program_checkin')->default(0);
                    $table->tinyInteger('show_wp')->default(1);
                    $table->tinyInteger('show_vp')->default(1);
                    $table->tinyInteger('show_qrcode')->default(1);
                    $table->tinyInteger('enable_email_ticket')->default(1);
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
