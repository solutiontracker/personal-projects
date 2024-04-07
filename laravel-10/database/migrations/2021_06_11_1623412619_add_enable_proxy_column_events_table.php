<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;
class AddEnableProxyColumnEventsTable extends Migration
{
    const TABLE = 'conf_events';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('enable_cloud_proxy')->nullable();
            $table->tinyInteger('enable_storage')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('enable_cloud_proxy')->nullable();
                $table->tinyInteger('enable_storage')->nullable();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('enable_cloud_proxy');
            $table->dropColumn('enable_storage');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('enable_cloud_proxy');
                $table->dropColumn('enable_storage');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
