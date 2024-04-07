<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParagraphNumberLineNumberToConfQaSettings extends Migration
{
    const TABLE = 'conf_qa_settings';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('paragraph_number')->default(0);
            $table->tinyInteger('enable_paragraph_number')->default(0);
            $table->tinyInteger('line_number')->default(0);
            $table->tinyInteger('enable_line_number')->default(0);
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('paragraph_number')->default(0);
                $table->tinyInteger('enable_paragraph_number')->default(0);
                $table->tinyInteger('line_number')->default(0);
                $table->tinyInteger('enable_line_number')->default(0);
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('paragraph_number');
            $table->dropColumn('enable_paragraph_number');
            $table->dropColumn('line_number');
            $table->dropColumn('enable_line_number');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('paragraph_number');
                $table->dropColumn('enable_paragraph_number');
                $table->dropColumn('line_number');
                $table->dropColumn('enable_line_number');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
