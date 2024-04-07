<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfOrganizerApnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_organizer_apns';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('organizer_id')->nullable()->index('organizer_id');
            $table->integer('linked_with');
            $table->string('key_id')->nullable();
            $table->string('team_id')->nullable();
            $table->string('apns_topic')->nullable();
            $table->text('private_key')->nullable();
            $table->text('jwt_token')->nullable();
            $table->string('issued_at', 100)->nullable()->comment('UNIX timestamp in UTC timezone');
            $table->tinyInteger('is_default')->default(0);
            $table->string('applicable_organizers', 256);
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('organizer_id')->nullable()->index('organizer_id');
                $table->integer('linked_with');
                $table->string('key_id')->nullable();
                $table->string('team_id')->nullable();
                $table->string('apns_topic')->nullable();
                $table->text('private_key')->nullable();
                $table->text('jwt_token')->nullable();
                $table->string('issued_at', 100)->nullable()->comment('UNIX timestamp in UTC timezone');
                $table->tinyInteger('is_default')->default(0);
                $table->string('applicable_organizers', 256);
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
