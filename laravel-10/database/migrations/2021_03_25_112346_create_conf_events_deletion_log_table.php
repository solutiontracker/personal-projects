<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventsDeletionLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_events_deletion_log';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('event_id')->index('event_id');
            $table->string('name')->nullable();
            $table->string('url')->nullable()->index('url');
            $table->integer('attendee_count')->default(0);
            $table->timestamp('soft_deleted_at')->nullable()->default('0000-00-00 00:00:00');
            $table->timestamp('hard_deleted_at')->default('0000-00-00 00:00:00');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('event_id')->index('event_id');
                $table->string('name')->nullable();
                $table->string('url')->nullable()->index('url');
                $table->integer('attendee_count')->default(0);
                $table->timestamp('soft_deleted_at')->nullable()->default('0000-00-00 00:00:00');
                $table->timestamp('hard_deleted_at')->default('0000-00-00 00:00:00');
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
