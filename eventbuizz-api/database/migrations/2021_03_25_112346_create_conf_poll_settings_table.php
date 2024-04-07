<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfPollSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_poll_settings';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('event_id')->index('event_id');
            $table->tinyInteger('tab');
            $table->tinyInteger('alerts');
            $table->tinyInteger('user_settings');
            $table->tinyInteger('display_poll')->default(1);
            $table->tinyInteger('display_survey')->default(1);
            $table->enum('tagcloud_shape', ['elliptic', 'rectangular', '', ''])->default('elliptic')->comment('1=Cloud,2-Rectangular');
            $table->string('tagcloud_colors');
            $table->bigInteger('projector_refresh_time')->default(30)->comment('In seconds');
            $table->string('font_size', 55);
            $table->tinyInteger('display_graph_logo')->default(1);
            $table->tinyInteger('display_graph_question_heading')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->tinyInteger('display_poll_module')->nullable()->default(1);
            $table->tinyInteger('display_survey_module')->nullable()->default(1);
            $table->bigInteger('projector_attendee_count');
            $table->tinyInteger('display_leader_board_attendee_image')->default(1);
            $table->tinyInteger('display_leader_board_attendee_title')->default(1);
            $table->tinyInteger('display_leader_board_attendee_company')->default(1);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('event_id')->index('event_id');
                $table->tinyInteger('tab');
                $table->tinyInteger('alerts');
                $table->tinyInteger('user_settings');
                $table->tinyInteger('display_poll')->default(1);
                $table->tinyInteger('display_survey')->default(1);
                $table->enum('tagcloud_shape', ['elliptic', 'rectangular', '', ''])->default('elliptic')->comment('1=Cloud,2-Rectangular');
                $table->string('tagcloud_colors');
                $table->bigInteger('projector_refresh_time')->default(30)->comment('In seconds');
                $table->string('font_size', 55);
                $table->tinyInteger('display_graph_logo')->default(1);
                $table->tinyInteger('display_graph_question_heading')->default(1);
                $table->timestamps();
            $table->softDeletes();
                $table->tinyInteger('display_poll_module')->nullable()->default(1);
                $table->tinyInteger('display_survey_module')->nullable()->default(1);
                $table->bigInteger('projector_attendee_count');
                $table->tinyInteger('display_leader_board_attendee_image')->default(1);
                $table->tinyInteger('display_leader_board_attendee_title')->default(1);
                $table->tinyInteger('display_leader_board_attendee_company')->default(1);
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
        EBSchema::dropDeleteTrigger(self::TABLE);
        Schema::dropIfExists(self::TABLE);
            Schema::connection(config('database.archive_connection'))->dropIfExists(self::TABLE);
    }
}
