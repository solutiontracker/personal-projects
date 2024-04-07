<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToConfEmailLog extends Migration
{
    const TABLE = 'conf_email_log';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->longText('transmission_id')->nullable();
            $table->longText('response')->nullable();
            $table->integer('bounce')->default(0);
            $table->integer('delivery')->default(0);
            $table->integer('click')->default(0);
            $table->integer('open')->default(0);
            $table->integer('organizer_id')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->longText('transmission_id')->nullable();
                $table->longText('response')->nullable();
                $table->integer('bounce')->default(0);
                $table->integer('delivery')->default(0);
                $table->integer('click')->default(0);
                $table->integer('open')->default(0);
                $table->integer('organizer_id')->nullable();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('transmission_id');
            $table->dropColumn('response');
            $table->dropColumn('bounce');
            $table->dropColumn('delivery');
            $table->dropColumn('click');
            $table->dropColumn('open');
            $table->dropColumn('organizer_id');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('transmission_id');
                $table->dropColumn('response');
                $table->dropColumn('bounce');
                $table->dropColumn('delivery');
                $table->dropColumn('click');
                $table->dropColumn('open');
                $table->dropColumn('organizer_id');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
