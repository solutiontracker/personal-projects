<?php
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfAgoraCallAnalyticsTable extends Migration
{
    const TABLE = 'conf_agora_call_analytics';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('event_id')->nullable()->index();
            $table->unsignedBigInteger('attendee_id')->nullable()->index();
            $table->string('agora_id', 100)->nullable()->index();
            $table->string('project_id', 100)->nullable()->index();
            $table->unsignedBigInteger('vid')->nullable()->index();
            $table->unsignedBigInteger('created_ts')->nullable();
            $table->unsignedBigInteger('destroyed_ts')->nullable();
            $table->string('cname')->nullable()->index();
            $table->unsignedBigInteger('cid')->nullable()->index();
            $table->tinyInteger('finished')->nullable();
            $table->unsignedBigInteger('ts')->nullable();
            $table->integer('mode')->nullable();
            $table->unsignedBigInteger('duration')->nullable();
            $table->tinyInteger('permanented')->nullable();
            $table->dateTime('created_ts_at')->nullable();
            $table->dateTime('destroyed_ts_at')->nullable();
            $table->dateTime('ts_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->unsignedBigInteger('event_id')->nullable();
                $table->unsignedBigInteger('attendee_id')->nullable();
                $table->string('agora_id', 100)->nullable();
                $table->string('project_id', 100)->nullable();
                $table->unsignedBigInteger('vid')->nullable();
                $table->unsignedBigInteger('created_ts')->nullable();
                $table->unsignedBigInteger('destroyed_ts')->nullable();
                $table->string('cname')->nullable();
                $table->unsignedBigInteger('cid')->nullable();
                $table->tinyInteger('finished')->nullable();
                $table->unsignedBigInteger('ts')->nullable();
                $table->integer('mode')->nullable();
                $table->unsignedBigInteger('duration')->nullable();
                $table->tinyInteger('permanented')->nullable();
                $table->dateTime('created_ts_at')->nullable();
                $table->dateTime('destroyed_ts_at')->nullable();
                $table->dateTime('ts_at')->nullable();
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
