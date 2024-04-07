<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventChatMessageTable extends Migration
    {
        const TABLE = 'conf_event_chat_message';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('thread_id');
                $table->timestamp('sent_date')->nullable();
                $table->text('body');
                $table->integer('sender_id');
                $table->index(['thread_id', 'sender_id'], 'thread_id_sender_id');
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('thread_id');
                    $table->timestamp('sent_date')->nullable();
                    $table->text('body');
                    $table->integer('sender_id');
                    $table->index(['thread_id', 'sender_id'], 'thread_id_sender_id');
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
