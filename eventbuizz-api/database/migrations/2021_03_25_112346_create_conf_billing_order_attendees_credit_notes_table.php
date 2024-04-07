<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfBillingOrderAttendeesCreditNotesTable extends Migration
    {
        const TABLE = 'conf_billing_order_attendees_credit_notes';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('order_id')->index('order_id');
                $table->bigInteger('credit_note_id')->index('credit_note_id');
                $table->bigInteger('order_number');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->integer('event_qty')->default(1);
                $table->float('event_discount', 10, 0);
                $table->timestamps();
                $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('order_id')->index('order_id');
                    $table->bigInteger('credit_note_id')->index('credit_note_id');
                    $table->bigInteger('order_number');
                    $table->bigInteger('attendee_id')->index('attendee_id');
                    $table->integer('event_qty')->default(1);
                    $table->float('event_discount', 10, 0);
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
