<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfOrganizerMediaLibraryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_organizer_media_library';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('organizer_id')->index('organizer_id');
            $table->string('file_name');
            $table->enum('type', ['attendees', 'sponsors', 'exhibitors', 'banners', 'header_logo', 'app_icon', 'eventsite_banners', 'favicon', 'invoice_logo', 'social_media_logo', 'eventsite_news', 'templates']);
            $table->string('original_filename', 100)->nullable();
            $table->string('size', 30)->nullable();
            $table->string('weight', 30)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('organizer_id')->index('organizer_id');
                $table->string('file_name');
                $table->enum('type', ['attendees', 'sponsors', 'exhibitors', 'banners', 'header_logo', 'app_icon', 'eventsite_banners', 'favicon', 'invoice_logo', 'social_media_logo', 'eventsite_news', 'templates']);
                $table->string('original_filename', 100)->nullable();
                $table->string('size', 30)->nullable();
                $table->string('weight', 30)->nullable();
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
