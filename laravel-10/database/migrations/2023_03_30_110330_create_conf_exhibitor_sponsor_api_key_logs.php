<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfExhibitorSponsorApiKeyLogs extends Migration
{
    const TABLE = 'conf_exhibitor_sponsor_api_key_logs';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('type_id');
            $table->string('type');
            $table->string('old_key')->nullable();
            $table->string('new_key')->nullable();
            $table->string('regenerate_email');
            $table->timestamp('regenerate_datetime')->default('0000-00-00 00:00:00');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('type_id');
                $table->string('type');
                $table->string('old_key')->nullable();
                $table->string('new_key')->nullable();
                $table->string('regenerate_email');
                $table->timestamp('regenerate_datetime')->default('0000-00-00 00:00:00');
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
