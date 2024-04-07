<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddBroadcastingIdColumnAgendaVideoTable extends Migration
{
    const TABLE = 'conf_agenda_video';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('broadcasting_id')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('broadcasting_id')->nullable();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('broadcasting_id');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('broadcasting_id');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
