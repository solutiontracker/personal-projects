<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfAttendeeInviteStatsTable extends Migration
{
    const TABLE = 'conf_attendee_invite_stats';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('organizer_id')->nullable();
            $table->integer('event_id')->nullable();
            $table->string('template_alias', 30)->nullable();
            $table->integer('open')->nullable()->default(0);
            $table->integer('click')->nullable()->default(0);
            $table->integer('reject')->nullable()->default(0);
            $table->integer('send')->default(0);
            $table->integer('deferral')->default(0);
            $table->integer('hard_bounce')->default(0);
            $table->integer('soft_bounce')->default(0);
            $table->string('email', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('organizer_id')->nullable();
                $table->integer('event_id')->nullable();
                $table->string('template_alias', 30)->nullable();
                $table->integer('open')->nullable()->default(0);
                $table->integer('click')->nullable()->default(0);
                $table->integer('reject')->nullable()->default(0);
                $table->integer('send')->default(0);
                $table->integer('deferral')->default(0);
                $table->integer('hard_bounce')->default(0);
                $table->integer('soft_bounce')->default(0);
                $table->string('email', 100)->nullable();
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
