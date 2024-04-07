<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddFileTitleToConfEventsiteDocumentsTable extends Migration
{
    const TABLE = 'conf_eventsite_documents';

    /**
     * Run the migrations.
     * 
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('file_title');
            $table->boolean('s3')->default(0);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('file_title');
                $table->boolean('s3')->default(0);
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
            $table->dropColumn('file_title');
            $table->dropColumn('s3');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('file_title');
                $table->dropColumn('s3');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
