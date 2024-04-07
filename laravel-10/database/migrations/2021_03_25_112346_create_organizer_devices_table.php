<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizerDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'organizer_devices';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('organizer_id')->index('organizer_id');
            $table->text('name')->nullable();
            $table->text('hash')->nullable();
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->longText('detail')->nullable();
            $table->text('location')->nullable();
            $table->string('ip')->nullable();
            $table->text('location_info')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('organizer_id')->index('organizer_id');
                $table->text('name')->nullable();
                $table->text('hash')->nullable();
                $table->dateTime('verified_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->longText('detail')->nullable();
                $table->text('location')->nullable();
                $table->string('ip')->nullable();
                $table->text('location_info')->nullable();
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
