<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfMailingListCampaignRssLogsTable extends Migration
    {
        const TABLE = 'conf_mailing_list_campaign_rss_logs';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('mailing_list_campaign_id');
                $table->string('title', 2000);
                $table->string('link', 2000)->nullable();
                $table->string('guid', 1000)->nullable();
                $table->string('pubDate', 1000)->nullable();
                $table->string('author', 1000)->nullable();
                $table->text('description')->nullable();
                $table->date('created_date')->default('0000-00-00');
                $table->time('created_time')->default('00:00:00');
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('mailing_list_campaign_id');
                    $table->string('title', 2000);
                    $table->string('link', 2000)->nullable();
                    $table->string('guid', 1000)->nullable();
                    $table->string('pubDate', 1000)->nullable();
                    $table->string('author', 1000)->nullable();
                    $table->text('description')->nullable();
                    $table->date('created_date')->default('0000-00-00');
                    $table->time('created_time')->default('00:00:00');
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
