<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventBadgesTable extends Migration
    {
        const TABLE = 'conf_event_badges';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->bigInteger('event_id')->index('event_id');
                $table->tinyInteger('template_type');
                $table->string('heading_color', 250);
                $table->string('company_color', 250);
                $table->string('tracks_color', 250);
                $table->string('delegate_Color', 250);
                $table->string('table_Color', 250);
                $table->string('logo', 250);
                $table->enum('logoType', ['default', 'square']);
                $table->string('footer_bg_color', 250);
                $table->string('footer_text_color', 250);
                $table->timestamps();
                $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->tinyInteger('template_type');
                    $table->string('heading_color', 250);
                    $table->string('company_color', 250);
                    $table->string('tracks_color', 250);
                    $table->string('delegate_Color', 250);
                    $table->string('table_Color', 250);
                    $table->string('logo', 250);
                    $table->enum('logoType', ['default', 'square']);
                    $table->string('footer_bg_color', 250);
                    $table->string('footer_text_color', 250);
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
