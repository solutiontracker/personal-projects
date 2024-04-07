<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyNameAndBoothNumberToConfExhibitorsSettings extends Migration
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
            $table->tinyInteger('allow_company_name')->default(1);
            $table->tinyInteger('allow_booth_number')->default(1);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('allow_company_name')->default(1);
                $table->tinyInteger('allow_booth_number')->default(1);
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
            $table->dropColumn('allow_company_name');
            $table->dropColumn('allow_booth_number');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('allow_company_name');
                $table->dropColumn('allow_booth_number');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
