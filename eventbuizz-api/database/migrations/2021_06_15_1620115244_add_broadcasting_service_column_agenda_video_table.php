<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddBroadCastingServiceColumnAgendaVideoTable extends Migration
{
    const TABLE = 'conf_agenda_video';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('broadcasting_service',10)->nullable();
            $table->dropColumn('broadcasting_type');
            $table->dropColumn('broadcasting');
            \DB::statement("ALTER TABLE ".self::TABLE." MODIFY COLUMN type ENUM('link', 'local', 'live', 'agora-realtime-broadcasting-custom', 'agora-realtime-broadcasting', 'agora-external-streaming', 'agora-rooms', 'agora-webinar', 'agora-panel-disscussions') DEFAULT 'link'");
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('broadcasting_service',10)->nullable();
                $table->dropColumn('broadcasting_type');
                $table->dropColumn('broadcasting');
                \DB::connection(config('database.archive_connection'))->statement("ALTER TABLE ".self::TABLE." MODIFY COLUMN type ENUM('link', 'local', 'live', 'agora-realtime-broadcasting-custom', 'agora-realtime-broadcasting', 'agora-external-streaming', 'agora-rooms', 'agora-webinar', 'agora-panel-disscussions') DEFAULT 'link'");
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('broadcasting')->nullable();
            $table->string('broadcasting_type')->nullable();
            $table->dropColumn('broadcasting_service');
            \DB::statement("ALTER TABLE ".self::TABLE." MODIFY COLUMN type ENUM('link', 'local', 'live', 'agora-realtime-broadcasting', 'agora-external-streaming', 'agora-rooms', 'agora-webinar', 'agora-panel-disscussions') DEFAULT 'link'");
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('broadcasting')->nullable();
                $table->string('broadcasting_type')->nullable();
                $table->dropColumn('broadcasting_service');
                \DB::connection(config('database.archive_connection'))->statement("ALTER TABLE ".self::TABLE." MODIFY COLUMN type ENUM('link', 'local', 'live', 'agora-realtime-broadcasting', 'agora-external-streaming', 'agora-rooms', 'agora-webinar', 'agora-panel-disscussions') DEFAULT 'link'");
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
