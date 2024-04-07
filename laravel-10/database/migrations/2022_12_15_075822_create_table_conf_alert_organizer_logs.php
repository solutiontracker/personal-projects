<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableConfAlertOrganizerLogs extends Migration
{
    const TABLE = 'conf_alert_organizer_logs';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organizer_id');
            $table->unsignedBigInteger('organizer_alert_id');
            $table->tinyInteger('send_by_email')->default(1)->comment('0=no; 1=yes');
            $table->tinyInteger('send_by_notification')->default(0)->comment('0=no; 1=yes');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('organizer_id');
                $table->unsignedBigInteger('organizer_alert_id');
                $table->tinyInteger('send_by_email')->default(1)->comment('0=no; 1=yes');
                $table->tinyInteger('send_by_notification')->default(0)->comment('0=no; 1=yes');
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
