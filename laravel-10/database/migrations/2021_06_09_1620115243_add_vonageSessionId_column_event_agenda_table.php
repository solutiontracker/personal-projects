<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddVonageSessionIdColumnEventAgendaTable extends Migration
{
    const TABLE = 'conf_event_agendas';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('vonageSessionId')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('vonageSessionId')->nullable();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('vonageSessionId');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('vonageSessionId');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
