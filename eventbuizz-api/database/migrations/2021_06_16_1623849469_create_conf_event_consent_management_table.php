<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfEventConsentManagementTable extends Migration
{
    const TABLE = 'conf_event_consent_management';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('event_id')->index('event_id')->nullable();
            $table->bigInteger('type_id')->index('type_id')->nullable();
            $table->enum('type', ['sponsor', 'exhibitor']);
            $table->string('consent_name')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->Integer('sort_order')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->bigInteger('event_id')->index('event_id')->nullable();
                $table->bigInteger('type_id')->index('type_id')->nullable();
                $table->enum('type', ['sponsor', 'exhibitor']);
                $table->string('consent_name')->nullable();
                $table->tinyInteger('status')->nullable();
                $table->Integer('sort_order')->nullable();
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
