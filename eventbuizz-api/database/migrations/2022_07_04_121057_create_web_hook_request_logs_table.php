<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebHookRequestLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_webhook_request_log';

    public function up()
    {
        Schema::connection('mysql_email_logs')->create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('type')->nullable();
            $table->longText('data')->nullable();
            $table->text('endpoint')->nullable();
            $table->dateTime('date')->nullable()->index('date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection(config('mysql_email_logs'))->dropIfExists(self::TABLE);
    }
}
