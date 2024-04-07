<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfBillingFieldsOriginalTable extends Migration
    {
        const TABLE = 'conf_billing_fields_original';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->integer('sort_order');
                $table->bigInteger('event_id')->index('event_id');
                $table->tinyInteger('status')->index('status');
                $table->tinyInteger('mandatory')->default(0)->index('mandatory');
                $table->string('field_alias', 250)->index('field_alias');
                $table->timestamps();
            $table->softDeletes();
                $table->enum('type', ['section', 'field'])->index('type');
                $table->string('section_alias', 250);
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('sort_order');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->tinyInteger('status')->index('status');
                    $table->tinyInteger('mandatory')->default(0)->index('mandatory');
                    $table->string('field_alias', 250)->index('field_alias');
                    $table->timestamps();
            $table->softDeletes();
                    $table->enum('type', ['section', 'field'])->index('type');
                    $table->string('section_alias', 250);
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
