<?php
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCatalogueColumnConfExhibitorsSettingsTable extends Migration
{
    const TABLE = 'conf_exhibitors_settings';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('catalogue_product')->nullable();
            $table->tinyInteger('consent_management')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('catalogue_product')->nullable();
                $table->tinyInteger('consent_management')->nullable();
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('catalogue_product');
            $table->tinyInteger('consent_management');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('catalogue_product');
                $table->tinyInteger('consent_management');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
