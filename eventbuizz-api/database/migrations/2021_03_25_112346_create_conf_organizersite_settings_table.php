<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfOrganizersiteSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_organizersite_settings';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('organizer_id')->index('organizer_id');
            $table->string('logo')->nullable();
            $table->tinyInteger('show_banner')->nullable()->default(1);
            $table->text('aboutus')->nullable();
            $table->text('contactus')->nullable();
            $table->integer('status')->index('status');
            $table->string('primary_color', 55);
            $table->string('secondary_color', 55);
            $table->string('tickets_background', 30);
            $table->string('time_left', 10);
            $table->dateTime('date_time');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('organizer_id')->index('organizer_id');
                $table->string('logo')->nullable();
                $table->tinyInteger('show_banner')->nullable()->default(1);
                $table->text('aboutus')->nullable();
                $table->text('contactus')->nullable();
                $table->integer('status')->index('status');
                $table->string('primary_color', 55);
                $table->string('secondary_color', 55);
                $table->string('tickets_background', 30);
                $table->string('time_left', 10);
                $table->dateTime('date_time');
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
