<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableConfAlertOrganizer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conf_alert_organizer', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organizer_alert_id');
            $table->unsignedBigInteger('organizer_id');
            $table->tinyInteger('read_status')->default(0);
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
        Schema::dropIfExists('conf_alert_organizer');
    }
}
