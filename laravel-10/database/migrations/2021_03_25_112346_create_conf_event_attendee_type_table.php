<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventAttendeeTypeTable extends Migration
    {
        const TABLE = 'conf_event_attendee_type';

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
                $table->integer('languages_id')->index('languages_id');
                $table->integer('sort_order');
                $table->string('alias', 55)->index('alias');
                $table->string('attendee_type', 55)->index('attendee_type');
                $table->tinyInteger('is_basic')->default(0)->index('is_basic');
                $table->tinyInteger('status')->default(1)->index('status')->comment('0=inactive, 1=active');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('event_id')->index('event_id');
                    $table->integer('languages_id')->index('languages_id');
                    $table->integer('sort_order');
                    $table->string('alias', 55)->index('alias');
                    $table->string('attendee_type', 55)->index('attendee_type');
                    $table->tinyInteger('is_basic')->default(0)->index('is_basic');
                    $table->tinyInteger('status')->default(1)->index('status')->comment('0=inactive, 1=active');
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
