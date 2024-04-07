<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddKeyColumnToConfBadgesPrinterQueue extends Migration
{
    const TABLE = 'conf_badges_printer_queue';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->text('mobile_key')->nullable();
            $table->text('mobile_key_2')->nullable();
            $table->text('mobile_key_3')->nullable();
            $table->text('mobile_key_4')->nullable();
            $table->text('mobile_key_5')->nullable();
            $table->text('mobile_key_6')->nullable();
            $table->text('mobile_key_7')->nullable();
            $table->text('mobile_key_8')->nullable();
            $table->text('mobile_key_9')->nullable();
            $table->text('mobile_key_10')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->text('mobile_key')->nullable();
                $table->text('mobile_key_2')->nullable();
                $table->text('mobile_key_3')->nullable();
                $table->text('mobile_key_4')->nullable();
                $table->text('mobile_key_5')->nullable();
                $table->text('mobile_key_6')->nullable();
                $table->text('mobile_key_7')->nullable();
                $table->text('mobile_key_8')->nullable();
                $table->text('mobile_key_9')->nullable();
                $table->text('mobile_key_10')->nullable();
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
            $table->dropColumn('mobile_key');
            $table->dropColumn('mobile_key2');
            $table->dropColumn('mobile_key3');
            $table->dropColumn('mobile_key4');
            $table->dropColumn('mobile_key5');
            $table->dropColumn('mobile_key6');
            $table->dropColumn('mobile_key7');
            $table->dropColumn('mobile_key8');
            $table->dropColumn('mobile_key9');
            $table->dropColumn('mobile_key10');
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('mobile_key');
                $table->dropColumn('mobile_key2');
                $table->dropColumn('mobile_key3');
                $table->dropColumn('mobile_key4');
                $table->dropColumn('mobile_key5');
                $table->dropColumn('mobile_key6');
                $table->dropColumn('mobile_key7');
                $table->dropColumn('mobile_key8');
                $table->dropColumn('mobile_key9');
                $table->dropColumn('mobile_key10');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
