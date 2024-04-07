<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfAddAttendeeLogTable extends Migration
{

    const TABLE = 'conf_add_attendee_log';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('attendee_id')->index('attendee_id');
            $table->bigInteger('event_id')->index('event_id');
            $table->bigInteger('organizer_id')->index('organizer_id');
            $table->string('type', 20)->index('type');
            $table->integer('status')->default(0)->index('status')->comment('0=pending;1=finsihed; 2= failed');
            $table->timestamps();
            $table->softDeletes();
        });
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('attendee_id');
                $table->bigInteger('event_id');
                $table->bigInteger('organizer_id');
                $table->string('type', 20);
                $table->integer('status')->default(0)->comment('0=pending;1=finsihed; 2= failed');
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
