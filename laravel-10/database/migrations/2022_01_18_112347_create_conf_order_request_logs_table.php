<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfOrderRequestLogsTable extends Migration
    {
        const TABLE = 'conf_order_request_logs';

        /**
         * Run the migrations.
         *
         * @return void
         */

        public function up()
        {
            if (app()->environment('live')) {

                Schema::connection(config('database.mysql_email_logs'))->create(self::TABLE, function (Blueprint $table) {

                    $table->bigInteger('id', true);
                    $table->longText('request')->nullable();
                    $table->string('url')->nullable();
                    $table->bigInteger('event_id')->default(0)->index()->nullable();
                    $table->bigInteger('order_id')->default(0)->index()->nullable();
                    $table->timestamps();
                    $table->softDeletes();
                });
            }
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            if (app()->environment('live')) {
                Schema::connection(config('database.mysql_email_logs'))->dropIfExists(self::TABLE);
            }
        }
    }
