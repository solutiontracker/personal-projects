<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfBillingItemsTable extends Migration
    {
        const TABLE = 'conf_billing_items';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->bigInteger('group_id')->index('group_id');
                $table->enum('group_type', ['single', 'multiple'])->default('single')->index('group_type');
                $table->enum('group_required', ['yes', 'no'])->default('no');
                $table->enum('group_is_expanded', ['yes', 'no'])->default('yes');
                $table->enum('link_to', ['none', 'program', 'track', 'workshop', 'attendee_group'])->default('none')->index('link_to');
                $table->string('link_to_id', 100)->nullable()->index('link_to_id');
                $table->integer('sort_order');
                $table->string('item_number', 250);
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('organizer_id')->index('organizer_id');
                $table->double('price', 11, 2);
                $table->double('vat', 11, 2)->default(0);
                $table->integer('qty');
                $table->integer('total_tickets');
                $table->integer('status')->index('status');
                $table->integer('ticket_item_id')->nullable()->index('ticket_item_id');
                $table->integer('is_free')->default(0)->index('is_free');
                $table->integer('is_default')->default(0)->index('is_default');
                $table->integer('is_required')->default(0)->index('is_required');
                $table->enum('type', ['event_fee', 'admin_fee', 'item', 'group'])->default('item')->index('type');
                $table->tinyInteger('is_internal')->default(0);
                $table->timestamps();
            $table->softDeletes();
                $table->tinyInteger('is_archive')->index('is_archive');
                $table->tinyInteger('is_ticket')->nullable()->default(0)->index('is_ticket');
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->bigInteger('group_id')->index('group_id');
                    $table->enum('group_type', ['single', 'multiple'])->default('single')->index('group_type');
                    $table->enum('group_required', ['yes', 'no'])->default('no');
                    $table->enum('group_is_expanded', ['yes', 'no'])->default('yes');
                    $table->enum('link_to', ['none', 'program', 'track', 'workshop', 'attendee_group'])->default('none')->index('link_to');
                    $table->string('link_to_id', 100)->nullable()->index('link_to_id');
                    $table->integer('sort_order');
                    $table->string('item_number', 250);
                    $table->bigInteger('event_id')->index('event_id');
                    $table->bigInteger('organizer_id')->index('organizer_id');
                    $table->double('price', 11, 2);
                    $table->double('vat', 11, 2)->default(0);
                    $table->integer('qty');
                    $table->integer('total_tickets');
                    $table->integer('status')->index('status');
                    $table->integer('ticket_item_id')->nullable()->index('ticket_item_id');
                    $table->integer('is_free')->default(0)->index('is_free');
                    $table->integer('is_default')->default(0)->index('is_default');
                    $table->integer('is_required')->default(0)->index('is_required');
                    $table->enum('type', ['event_fee', 'admin_fee', 'item', 'group'])->default('item')->index('type');
                    $table->tinyInteger('is_internal')->default(0);
                    $table->timestamps();
            $table->softDeletes();
                    $table->tinyInteger('is_archive')->index('is_archive');
                    $table->tinyInteger('is_ticket')->nullable()->default(0)->index('is_ticket');
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
