<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddShowEventsiteBreadcrumbsToConfEventsiteSettings extends Migration
{
    const TABLE = 'conf_eventsite_settings';

    /**
     * Run the migrations.
     * 
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->boolean('show_eventsite_breadcrumbs')->default(1);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->boolean('show_eventsite_breadcrumbs')->default(1);
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
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('show_eventsite_breadcrumbs');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('show_eventsite_breadcrumbs');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
