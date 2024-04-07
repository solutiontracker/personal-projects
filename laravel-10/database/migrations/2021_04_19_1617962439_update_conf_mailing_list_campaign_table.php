<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;
class UpdateConfMailingListCampaignTable extends Migration
{
    const TABLE = 'conf_mailing_list_campaign';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('link_type',111)->after('rss_link')->nullable()->change();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('link_type',111)->after('rss_link')->nullable()->change();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('link_type')->nullable(false)->change();
        });
        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('link_type')->nullable(false)->change();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
