<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class UpdateConfMailingListCampaignRssLogsTable extends Migration
{
    const TABLE = 'conf_mailing_list_campaign_rss_logs';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->text('image')->after('guid');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->text('image')->after('guid');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('image');
        });
        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('image');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
