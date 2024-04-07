<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddVariationBackgroundToConfOrganizerMediaLibraryTable extends Migration
{
    const TABLE = 'conf_organizer_media_library';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \DB::statement("ALTER TABLE `conf_organizer_media_library` CHANGE `type` `type` ENUM('attendees','sponsors','exhibitors','banners','header_logo','app_icon','eventsite_banners','favicon','invoice_logo','social_media_logo','templates','virtual_app_logo','eventsite_news','general_info','practical_information','event_info','additional_info','hub_panel','videos','maps','virtual-branding','variation_background','event_site/upload_images') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;");
        if (app()->environment('live')) {
            \DB::statement("ALTER TABLE `conf_organizer_media_library` CHANGE `type` `type` ENUM('attendees','sponsors','exhibitors','banners','header_logo','app_icon','eventsite_banners','favicon','invoice_logo','social_media_logo','templates','virtual_app_logo','eventsite_news','general_info','practical_information','event_info','additional_info','hub_panel','videos','maps','virtual-branding','variation_background','event_site/upload_images') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL;");
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
        if (app()->environment('live')) {
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
