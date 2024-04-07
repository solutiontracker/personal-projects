<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailStatsLogInfosTable extends Migration
{
    const TABLE = 'conf_email_stats_log_infos';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_email_logs')->create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->string('from')->nullable();
            $table->text('headers')->nullable()->default('');
            $table->longText('body')->nullable()->default('');
            $table->longText('response')->nullable()->default('');
            $table->bigInteger('email_stats_log_id');
            $table->string('template')->nullable();
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
        Schema::dropIfExists(self::TABLE);
    }
}
