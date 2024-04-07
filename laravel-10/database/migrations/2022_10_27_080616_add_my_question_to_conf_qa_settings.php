<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMyQuestionToConfQaSettings extends Migration
{
    const TABLE = 'conf_qa_settings';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('popular')->default(1);
            $table->tinyInteger('recent')->default(1);
            $table->tinyInteger('my_question')->default(1);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('popular')->default(1);
                $table->tinyInteger('recent')->default(1);
                $table->tinyInteger('my_question')->default(1);
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
            $table->dropColumn('popular');
            $table->dropColumn('recent');
            $table->dropColumn('my_question');
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table('conf_attendee_invites', function (Blueprint $table) {
                $table->dropColumn('popular');
                $table->dropColumn('recent');
                $table->dropColumn('my_question');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
