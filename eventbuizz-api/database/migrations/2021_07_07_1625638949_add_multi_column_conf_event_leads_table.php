<?php
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultiColumnConfEventLeadsTable extends Migration
{
    const TABLE = 'conf_event_leads';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('profile_image_data')->nullable();
            $table->string('catalogue_products_id')->nullable();
            $table->string('consent_management_id')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('profile_image_data')->nullable();
                $table->string('catalogue_products_id')->nullable();
                $table->string('consent_management_id')->nullable();
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('profile_image_data');
            $table->string('catalogue_products_id');
            $table->string('consent_management_id');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('profile_image_data');
                $table->string('catalogue_products_id');
                $table->string('consent_management_id');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
