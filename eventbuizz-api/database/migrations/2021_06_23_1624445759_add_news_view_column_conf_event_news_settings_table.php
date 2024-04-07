<?php
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewsViewColumnConfEventNewsSettingsTable extends Migration
{
    const TABLE = 'conf_event_news_settings';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->enum('news_view', ['default', 'layout_1', 'layout_2', 'layout_3'])->nullable()->default('default');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->enum('news_view', ['default', 'layout_1', 'layout_2', 'layout_3'])->nullable()->default('default');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->enum('news_view');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->enum('news_view');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
