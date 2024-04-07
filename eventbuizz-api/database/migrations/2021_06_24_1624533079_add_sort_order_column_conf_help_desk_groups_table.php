<?php
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSortOrderColumnConfHelpDeskGroupsTable extends Migration
{
    const TABLE = 'conf_help_desk_groups';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('sort_order')->default(0)->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('sort_order')->default(0)->nullable();
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('sort_order');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('sort_order');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
