<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventAgendaAttendeeAttachedTable extends Migration
    {
        const TABLE = 'conf_event_agenda_attendee_attached';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->bigInteger('agenda_id')->index('agenda_id');
                $table->tinyInteger('added_by')->default(0)->index('added_by');
                $table->enum('linked_from', ['', 'billing_item', 'subregistration', 'misc'])->default('')->index('linked_from');
                $table->bigInteger('link_id')->index('link_id');
                $table->timestamps();
            $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->bigInteger('attendee_id')->index('attendee_id');
                    $table->bigInteger('agenda_id')->index('agenda_id');
                    $table->tinyInteger('added_by')->default(0)->index('added_by');
                    $table->enum('linked_from', ['', 'billing_item', 'subregistration', 'misc'])->default('')->index('linked_from');
                    $table->bigInteger('link_id')->index('link_id');
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
