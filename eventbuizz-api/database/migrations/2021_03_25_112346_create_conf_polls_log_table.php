<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfPollsLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_polls_log';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('event_id')->index('event_id');
            $table->bigInteger('attendee_id')->index('attendee_id');
            $table->bigInteger('poll_id')->index('poll_id');
            $table->integer('status')->index('status')->comment('1=Rendered,2=Submitted');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->bigInteger('poll_id')->index('poll_id');
                $table->integer('status')->index('status')->comment('1=Rendered,2=Submitted');
                $table->timestamps();
                $table->softDeletes();
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
