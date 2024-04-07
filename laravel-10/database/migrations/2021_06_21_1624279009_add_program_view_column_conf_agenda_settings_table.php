<?php
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProgramViewColumnConfAgendaSettingsTable extends Migration
{
    const TABLE = 'conf_agenda_settings';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->enum('program_view', ['default', 'vertical', 'horizontal'])->default('default')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->enum('program_view', ['default', 'vertical', 'horizontal'])->default('default')->nullable();
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->enum('program_view');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->enum('program_view');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
