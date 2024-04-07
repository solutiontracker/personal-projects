<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfBillingOrderAddonsCreditNotesTable extends Migration
    {
        const TABLE = 'conf_billing_order_addons_credit_notes';

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
                $table->integer('addon_id')->index('addon_id');
                $table->string('name', 250);
                $table->float('price', 10, 0);
                $table->float('vat', 10, 0)->default(0);
                $table->integer('qty')->default(1);
                $table->float('discount', 10, 0);
                $table->bigInteger('parent')->index('parent');
                $table->enum('link_to', ['none', 'program', 'track', 'workshop'])->default('none')->index('link_to');
                $table->bigInteger('link_to_id')->index('link_to_id');
                $table->integer('group_id')->default(0)->index('group_id');
                $table->timestamps();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('order_id')->index('order_id');
                    $table->bigInteger('credit_note_id')->index('credit_note_id');
                    $table->bigInteger('order_number');
                    $table->bigInteger('attendee_id')->index('attendee_id');
                    $table->integer('addon_id')->index('addon_id');
                    $table->string('name', 250);
                    $table->float('price', 10, 0);
                    $table->float('vat', 10, 0)->default(0);
                    $table->integer('qty')->default(1);
                    $table->float('discount', 10, 0);
                    $table->bigInteger('parent')->index('parent');
                    $table->enum('link_to', ['none', 'program', 'track', 'workshop'])->default('none')->index('link_to');
                    $table->bigInteger('link_to_id')->index('link_to_id');
                    $table->integer('group_id')->default(0)->index('group_id');
                    $table->timestamps();
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
