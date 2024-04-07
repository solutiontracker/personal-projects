<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventRegistrationSiteHeaderFooterContentTable extends Migration
{
    const TABLE = 'conf_event_registration_site_header_footer_content';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id');
            $table->string('organizer_name');
            $table->bigInteger('country_id')->nullable()->default(0);
            $table->string('address');
            $table->string('zip');
            $table->timestamps();
            $table->softDeletes();

        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id');
                $table->string('organizer_name');
                $table->bigInteger('country_id')->nullable()->default(0);
                $table->string('address');
                $table->string('zip');
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
