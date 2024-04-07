<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfActorActivityLogTable extends Migration
{
    const TABLE = 'conf_actor_activity_log';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('module_alias')->index('module_alias');
            $table->integer('actor_id')->index('actor_id');
            $table->enum('actor_type', ['super', 'admin', 'demo', 'readonly', 'attendee', 'subscriber'])->index('actor_type');
            $table->enum('action', ['deleted', 'unsubscribed'])->index('action');
            $table->text('activity');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->string('module_alias');
                $table->integer('actor_id');
                $table->enum('actor_type', ['super', 'admin', 'demo', 'readonly', 'attendee', 'subscriber']);
                $table->enum('action', ['deleted', 'unsubscribed']);
                $table->text('activity');
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
