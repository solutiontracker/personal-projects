<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventMessageRecipientsTable extends Migration
    {
        const TABLE = 'conf_event_message_recipients';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->unsignedInteger('mid');
                $table->unsignedInteger('seq');
                $table->bigInteger('receiver')->index('receiver');
                $table->text('all_recipients');
                $table->bigInteger('event_id')->index('event_id');
                $table->tinyInteger('status')->default(1)->index('status')->comment('1=New, 2=Read,3=Delete');
                $table->timestamps();
            $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->unsignedInteger('mid');
                    $table->unsignedInteger('seq');
                    $table->bigInteger('receiver')->index('receiver');
                    $table->text('all_recipients');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->tinyInteger('status')->default(1)->index('status')->comment('1=New, 2=Read,3=Delete');
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
