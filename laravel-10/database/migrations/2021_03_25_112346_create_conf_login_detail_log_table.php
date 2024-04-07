<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfLoginDetailLogTable extends Migration
{
    const TABLE = 'conf_login_detail_log';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('attendee_id')->index('attendee_id');
            $table->integer('event_id')->index('event_id');
            $table->integer('organizer_id')->index('organizer_id');
            $table->integer('disclaimer_id')->index('disclaimer_id');
            $table->dateTime('login_date');
            $table->dateTime('disclaimer_date')->index('disclaimer_date');
            $table->string('device');
            $table->string('ip_address', 55);
            $table->text('disclaimer_version');
            $table->timestamps();
            $table->softDeletes();
        });


        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('attendee_id')->index('attendee_id');
                $table->integer('event_id')->index('event_id');
                $table->integer('organizer_id')->index('organizer_id');
                $table->integer('disclaimer_id')->index('disclaimer_id');
                $table->dateTime('login_date');
                $table->dateTime('disclaimer_date')->index('disclaimer_date');
                $table->string('device');
                $table->string('ip_address', 55);
                $table->text('disclaimer_version');
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
