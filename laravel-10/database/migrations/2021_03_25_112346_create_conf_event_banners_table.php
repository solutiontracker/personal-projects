<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventBannersTable extends Migration
    {
        const TABLE = 'conf_event_banners';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('sponsor_id')->index('sponsor_id');
                $table->bigInteger('exhibitor_id')->index('exhibitor_id');
                $table->bigInteger('agenda_id')->nullable()->default(0);
                $table->string('other_link_url');
                $table->bigInteger('sort_order')->index('sort_order');
                $table->tinyInteger('status')->default(1)->index('status');
                $table->enum('banner_type', ['web_app', 'native_app'])->default('web_app');
                $table->enum('banner_position', ['before_program', 'after_program', 'before_speaker', 'after_speaker', 'before_sponsor', 'after_sponsor', 'before_exhibitor', 'after_exhibitor', 'before_my_program', 'after_my_program', 'before_polls', 'after_polls', 'before_survey', 'after_survey', 'before_news_update', 'after_news_update'])->default('before_program');
                $table->dateTime('start_date');
                $table->dateTime('end_date');
                $table->timestamps();
            $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->bigInteger('sponsor_id')->index('sponsor_id');
                    $table->bigInteger('exhibitor_id')->index('exhibitor_id');
                    $table->bigInteger('agenda_id')->nullable()->default(0);
                    $table->string('other_link_url');
                    $table->bigInteger('sort_order')->index('sort_order');
                    $table->tinyInteger('status')->default(1)->index('status');
                    $table->enum('banner_type', ['web_app', 'native_app'])->default('web_app');
                    $table->enum('banner_position', ['before_program', 'after_program', 'before_speaker', 'after_speaker', 'before_sponsor', 'after_sponsor', 'before_exhibitor', 'after_exhibitor', 'before_my_program', 'after_my_program', 'before_polls', 'after_polls', 'before_survey', 'after_survey', 'before_news_update', 'after_news_update'])->default('before_program');
                    $table->dateTime('start_date');
                    $table->dateTime('end_date');
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
