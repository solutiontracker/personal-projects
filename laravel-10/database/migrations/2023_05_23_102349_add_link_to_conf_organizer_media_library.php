<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class AddLinkToConfOrganizerMediaLibrary extends Migration
{
    const TABLE = 'conf_organizer_media_library';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('link_id',30)->nullable();
            $table->string('link_type',30)->nullable();

        });
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
            $table->string('link_id',30)->nullable();
            $table->string('link_type',30)->nullable();
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
            $table->dropColumn('link_id');
            $table->dropColumn('link_type');
        });
        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
              $table->dropColumn('link_id');
              $table->dropColumn('link_type');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
