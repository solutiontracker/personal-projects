<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeFloorplansToConfOrganizerMediaLibrary extends Migration
{

    const TABLE = "conf_organizer_media_library";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            DB::statement("ALTER TABLE conf_organizer_media_library MODIFY COLUMN type ENUM('attendees','sponsors','exhibitors','banners','header_logo','app_icon','eventsite_banners','favicon','invoice_logo','social_media_logo','templates','virtual_app_logo','eventsite_news','general_info','practical_information','event_info','additional_info','hub_panel','videos','maps','virtual-branding','variation_background','event_site/upload_images', 'floorplans')");
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                DB::statement("ALTER TABLE conf_organizer_media_library MODIFY COLUMN type ENUM('attendees','sponsors','exhibitors','banners','header_logo','app_icon','eventsite_banners','favicon','invoice_logo','social_media_logo','templates','virtual_app_logo','eventsite_news','general_info','practical_information','event_info','additional_info','hub_panel','videos','maps','virtual-branding','variation_background','event_site/upload_images', 'floorplans')");
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            DB::statement("ALTER TABLE conf_organizer_media_library MODIFY COLUMN type ENUM('attendees','sponsors','exhibitors','banners','header_logo','app_icon','eventsite_banners','favicon','invoice_logo','social_media_logo','templates','virtual_app_logo','eventsite_news','general_info','practical_information','event_info','additional_info','hub_panel','videos','maps','virtual-branding','variation_background','event_site/upload_images')");
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                DB::statement("ALTER TABLE conf_organizer_media_library MODIFY COLUMN type ENUM('attendees','sponsors','exhibitors','banners','header_logo','app_icon','eventsite_banners','favicon','invoice_logo','social_media_logo','templates','virtual_app_logo','eventsite_news','general_info','practical_information','event_info','additional_info','hub_panel','videos','maps','virtual-branding','variation_background','event_site/upload_images')");
            });

        }
    }
}
