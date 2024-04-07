<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventCheckinTicketOrdersTable extends Migration
    {
        const TABLE = 'conf_event_checkin_ticket_orders';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('event_id');
                $table->integer('organizer_id');
                $table->integer('user_id');
                $table->date('order_date');
                $table->tinyInteger('is_archive')->default(0);
                $table->enum('status', ['cancelled', 'completed']);
                $table->string('user_type', 200)->nullable();
                $table->timestamps();
                $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('event_id');
                    $table->integer('organizer_id');
                    $table->integer('user_id');
                    $table->date('order_date');
                    $table->tinyInteger('is_archive')->default(0);
                    $table->enum('status', ['cancelled', 'completed']);
                    $table->string('user_type', 200)->nullable();
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
