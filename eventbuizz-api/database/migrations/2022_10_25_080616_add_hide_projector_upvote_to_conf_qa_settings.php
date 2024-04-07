<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHideProjectorUpvoteToConfQaSettings extends Migration
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
            $table->tinyInteger('hide_projector_upvote')->default(1);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('hide_projector_upvote')->default(1);
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
            $table->dropColumn('hide_projector_upvote');
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table('conf_attendee_invites', function (Blueprint $table) {
                $table->dropColumn('hide_projector_upvote');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
