<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventCheckinTicketItemsTable extends Migration
    {
        const TABLE = 'conf_event_checkin_ticket_items';

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
                $table->text('item_number')->nullable();
                $table->text('item_name');
                $table->integer('price')->nullable()->default(0);
                $table->integer('total_tickets')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->tinyInteger('status')->nullable()->comment('0=de-active,1=active');
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('event_id');
                    $table->integer('organizer_id');
                    $table->text('item_number')->nullable();
                    $table->text('item_name');
                    $table->integer('price')->nullable()->default(0);
                    $table->integer('total_tickets')->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                    $table->tinyInteger('status')->nullable()->comment('0=de-active,1=active');
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
