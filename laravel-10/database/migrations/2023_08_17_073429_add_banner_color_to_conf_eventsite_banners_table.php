<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddBannerColorToConfEventsiteBannersTable extends Migration
{
    const TABLE = 'conf_eventsite_banners';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('title_color',55)->default('#ffffff');
            $table->string('sub_title_color',55)->default('#ffffff');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('title_color',55)->default('#ffffff');
                $table->string('sub_title_color',55)->default('#ffffff');
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
            $table->dropColumn('title_color');
            $table->dropColumn('sub_title_color');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('title_color');
                $table->dropColumn('sub_title_color');
            });
            
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
