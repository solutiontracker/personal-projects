<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfEmailsLogsTableToEmailLogsDb extends Migration
{
    const TABLE = 'conf_email_log';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mysql_email_logs')->create(self::TABLE, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('event_id')->index('event_id')->nullable();
            $table->string('to', 100)->index('to')->nullable();
            $table->string('from', 100)->index('from')->nullable();
            $table->string('cc', 100)->index('cc')->nullable();
            $table->string('bcc', 100)->index('bcc')->nullable();
            $table->string('subject', 255)->index('subject')->nullable();
            $table->string('template', 255)->nullable();
            $table->text('headers')->nullable();
            $table->longText('body')->nullable();
            $table->longText('transmission_id')->nullable();
            $table->longText('response')->nullable();
            $table->integer('bounce')->default(0);
            $table->integer('delivery')->default(0);
            $table->integer('click')->default(0);
            $table->integer('open')->default(0);
            $table->integer('organizer_id')->index('organizer_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

    }

    public function down()
    {
        Schema::connection('mysql_email_logs')->dropIfExists(self::TABLE);
    }
}
