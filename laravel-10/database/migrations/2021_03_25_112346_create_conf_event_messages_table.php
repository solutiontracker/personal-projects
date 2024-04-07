<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventMessagesTable extends Migration
    {
        const TABLE = 'conf_event_messages';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('mid');
                $table->bigInteger('event_id')->index('FK_EVENT_ID');
                $table->bigInteger('group_id')->index('group_id');
                $table->unsignedInteger('seq')->default(1);
                $table->timestamp('created_on')->useCurrent();
                $table->bigInteger('created_by')->index('FK_ATENDEE_ID');
                $table->string('subject');
                $table->text('body');
                $table->unique(['mid', 'seq']);
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('mid');
                    $table->bigInteger('event_id')->index('FK_EVENT_ID');
                    $table->bigInteger('group_id')->index('group_id');
                    $table->unsignedInteger('seq')->default(1);
                    $table->timestamp('created_on')->useCurrent();
                    $table->bigInteger('created_by')->index('FK_ATENDEE_ID');
                    $table->string('subject');
                    $table->text('body');
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
