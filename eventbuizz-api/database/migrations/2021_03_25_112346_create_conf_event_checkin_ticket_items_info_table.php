<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventCheckinTicketItemsInfoTable extends Migration
    {
        const TABLE = 'conf_event_checkin_ticket_items_info';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('name', 200)->nullable();
                $table->text('value')->nullable();
                $table->integer('ticket_item_id')->nullable();
                $table->integer('languages_id')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->
                create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->string('name', 200)->nullable();
                    $table->text('value')->nullable();
                    $table->integer('ticket_item_id')->nullable();
                    $table->integer('languages_id')->nullable();
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
