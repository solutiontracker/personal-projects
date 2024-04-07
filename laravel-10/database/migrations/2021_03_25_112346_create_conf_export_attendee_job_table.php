<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfExportAttendeeJobTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_export_attendee_job';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('event_id')->index('event_id');
            $table->integer('key_id')->index('key_id');
            $table->string('key_name')->index('key_name');
            $table->string('model_name')->index('model_name');
            $table->string('file_name');
            $table->string('email');
            $table->longText('ids');
            $table->integer('status')->index('status');
            $table->longText('data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('event_id')->index('event_id');
                $table->integer('key_id')->index('key_id');
                $table->string('key_name')->index('key_name');
                $table->string('model_name')->index('model_name');
                $table->string('file_name');
                $table->string('email');
                $table->longText('ids');
                $table->integer('status')->index('status');
                $table->longText('data')->nullable();
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
