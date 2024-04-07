<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexToUserIdToConfBlogTemplates extends Migration
{
    const TABLE = 'conf_blog_templates';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->index('user_id');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->index('user_id');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropIndex(['user_id']);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropIndex(['user_id']);
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
