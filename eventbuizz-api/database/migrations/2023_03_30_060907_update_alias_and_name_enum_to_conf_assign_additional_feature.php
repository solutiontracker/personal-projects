<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateAliasAndNameEnumToConfAssignAdditionalFeature extends Migration
{
    const TABLE = 'conf_assign_additional_feature';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            DB::statement("ALTER TABLE conf_assign_additional_feature MODIFY COLUMN name ENUM('Internal Organizer', 'Allow business card', 'Event calendar API key', 'Email marketing template', 'Mailing list', 'Eventbuizz native app', 'White label native app', 'Allow NEM Id', 'Access plug n play', 'Membership list', 'Exhibitor portal API key')");
            DB::statement("ALTER TABLE conf_assign_additional_feature MODIFY COLUMN alias ENUM('internal_organizer', 'allow_card_reader', 'allow_api', 'email_marketing_template', 'mailing_list', 'eventbuizz_app', 'white_label_app', 'allow_nem_id', 'allow_plug_and_play_access','membership_list','exhibitor_api_key')");
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                DB::statement("ALTER TABLE conf_assign_additional_feature MODIFY COLUMN name ENUM('Internal Organizer', 'Allow business card', 'Event calendar API key', 'Email marketing template', 'Mailing list', 'Eventbuizz native app', 'White label native app', 'Allow NEM Id', 'Access plug n play', 'Membership list', 'Exhibitor portal API key')");
                DB::statement("ALTER TABLE conf_assign_additional_feature MODIFY COLUMN alias ENUM('internal_organizer', 'allow_card_reader', 'allow_api', 'email_marketing_template', 'mailing_list', 'eventbuizz_app', 'white_label_app', 'allow_nem_id', 'allow_plug_and_play_access','membership_list','exhibitor_api_key')");
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}