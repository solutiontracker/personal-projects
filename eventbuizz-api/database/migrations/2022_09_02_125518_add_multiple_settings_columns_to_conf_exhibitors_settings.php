<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultipleSettingsColumnsToConfExhibitorsSettings extends Migration
{
    const TABLE = 'conf_exhibitors_settings';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('show_blogs')->default(1);
            $table->tinyInteger('show_exhibitor_roi')->default(1);
            $table->tinyInteger('show_company_profile')->default(1);
            $table->tinyInteger('show_booth_staff')->default(1);
            $table->tinyInteger('show_company_documents')->default(1);
            $table->tinyInteger('show_leads')->default(1);
            $table->tinyInteger('show_template')->default(1);
            $table->tinyInteger('show_setting')->default(1);
            $table->tinyInteger('show_lead_scan_confirmation')->default(1);
            $table->tinyInteger('show_lead_user_promotion')->default(1);
            $table->tinyInteger('show_billing_history')->default(1);
            $table->tinyInteger('show_forms_listing')->default(1);
            $table->tinyInteger('show_response_overview')->default(1);
            $table->tinyInteger('show_download_lead_app')->default(1);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('show_blogs')->default(1);
                $table->tinyInteger('show_exhibitor_roi')->default(1);
                $table->tinyInteger('show_company_profile')->default(1);
                $table->tinyInteger('show_booth_staff')->default(1);
                $table->tinyInteger('show_company_documents')->default(1);
                $table->tinyInteger('show_leads')->default(1);
                $table->tinyInteger('show_template')->default(1);
                $table->tinyInteger('show_setting')->default(1);
                $table->tinyInteger('show_lead_scan_confirmation')->default(1);
                $table->tinyInteger('show_lead_user_promotion')->default(1);
                $table->tinyInteger('show_billing_history')->default(1);
                $table->tinyInteger('show_forms_listing')->default(1);
                $table->tinyInteger('show_response_overview')->default(1);
                $table->tinyInteger('show_download_lead_app')->default(1);
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
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('show_blogs');
            $table->dropColumn('show_company_profile');
            $table->dropColumn('show_booth_staff');
            $table->dropColumn('show_exhibitor_roi');
            $table->dropColumn('show_leads');
            $table->dropColumn('show_template');
            $table->dropColumn('show_setting');
            $table->dropColumn('show_company_documents');
            $table->dropColumn('show_lead_scan_confirmation');
            $table->dropColumn('show_lead_user_promotion');
            $table->dropColumn('show_billing_history');
            $table->dropColumn('show_forms_listing');
            $table->dropColumn('show_response_overview');
            $table->dropColumn('show_download_lead_app');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('show_blogs');
                $table->dropColumn('show_company_profile');
                $table->dropColumn('show_booth_staff');
                $table->dropColumn('show_exhibitor_roi');
                $table->dropColumn('show_leads');
                $table->dropColumn('show_template');
                $table->dropColumn('show_setting');
                $table->dropColumn('show_company_documents');
                $table->dropColumn('show_lead_scan_confirmation');
                $table->dropColumn('show_lead_user_promotion');
                $table->dropColumn('show_billing_history');
                $table->dropColumn('show_forms_listing');
                $table->dropColumn('show_response_overview');
                $table->dropColumn('show_download_lead_app');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
