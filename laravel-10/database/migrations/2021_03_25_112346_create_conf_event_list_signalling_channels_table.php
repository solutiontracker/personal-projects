<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventListSignallingChannelsTable extends Migration
    {

        const TABLE = 'conf_event_list_signalling_channels';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('event_id')->nullable();
                $table->integer('agenda_id')->nullable();
                $table->integer('attendee_id')->nullable();
                $table->string('ChannelName')->nullable();
                $table->string('ChannelARN')->nullable();
                $table->string('ChannelType')->nullable();
                $table->string('ChannelStatus')->nullable();
                $table->enum('type', ['myturnlist'])->nullable();
                $table->tinyInteger('is_like')->nullable()->default(0)->comment('2 = Not like, 1 = like');
                $table->timestamps();
                $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('event_id')->nullable();
                    $table->integer('agenda_id')->nullable();
                    $table->integer('attendee_id')->nullable();
                    $table->string('ChannelName')->nullable();
                    $table->string('ChannelARN')->nullable();
                    $table->string('ChannelType')->nullable();
                    $table->string('ChannelStatus')->nullable();
                    $table->enum('type', ['myturnlist'])->nullable();
                    $table->tinyInteger('is_like')->nullable()->default(0)->comment('2 = Not like, 1 = like');
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
