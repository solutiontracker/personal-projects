<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfSessionsNewTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_sessions_new';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->string('id')->unique('id');
            $table->bigInteger('attendee_id')->nullable()->index('attendee_id');
            $table->string('user_email')->nullable()->index('user_email');
            $table->bigInteger('event_id')->nullable()->index('event_id');
            $table->tinyInteger('site_type')->index('site_type')->comment('0=Admin,1=Front');
            $table->string('ip_address');
            $table->text('user_agent');
            $table->text('payload');
            $table->integer('last_activity')->index('last_activity');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->string('id');
                $table->bigInteger('attendee_id')->nullable()->index('attendee_id');
                $table->string('user_email')->nullable()->index('user_email');
                $table->bigInteger('event_id')->nullable()->index('event_id');
                $table->tinyInteger('site_type')->index('site_type')->comment('0=Admin,1=Front');
                $table->string('ip_address');
                $table->text('user_agent');
                $table->text('payload');
                $table->integer('last_activity')->index('last_activity');
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
