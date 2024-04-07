<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailStatsLogsTable extends Migration
{
    const TABLE = 'conf_email_stats_logs';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_email_logs')->create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->integer('event_id')->default(0)->nullable()->index('event_id');
            $table->integer('organizer_id')->default(0)->nullable()->index('organizer_id');
            $table->string('transmission_id')->nullable()->index('transmission_id');
            $table->string('to')->nullable()->index('to');
            $table->text('cc')->nullable();
            $table->text('bcc')->nullable();
            $table->string('subject')->nullable()->index('subject');
            $table->integer('bounce')->default(0);
            $table->integer('delivery')->default(0);
            $table->integer('click')->default(0);
            $table->integer('open')->default(0);
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
