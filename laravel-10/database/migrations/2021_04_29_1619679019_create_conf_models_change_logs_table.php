<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfModelsChangeLogsTable extends Migration
{
    const TABLE = 'conf_models_change_logs';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('organizer_id')->index('organizer_id')->nullable();
            $table->bigInteger('event_id')->index('event_id')->nullable();
            $table->bigInteger('model_id')->index('model_id')->nullable();
            $table->string('module_alias',255)->index('module_alias')->nullable();
            $table->string('action',255)->nullable();
            $table->string('action_model',255)->nullable();
            $table->string('changed_column',255)->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('organizer_id')->index('organizer_id')->nullable();
                $table->bigInteger('event_id')->index('event_id')->nullable();
                $table->bigInteger('model_id')->index('model_id')->nullable();
                $table->string('module_alias',255)->index('module_alias')->nullable();
                $table->string('action',255)->nullable();
                $table->string('action_model',255)->nullable();
                $table->string('changed_column',255)->nullable();
                $table->text('old_value')->nullable();
                $table->text('new_value')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        EBSchema::dropDeleteTrigger(self::TABLE);
        Schema::dropIfExists(self::TABLE);
        Schema::connection(config('database.archive_connection'))->dropIfExists(self::TABLE);
    }
}
