<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;


class AddSortOrderAndStatusToConfFormBuilderForms extends Migration
{
    const TABLE = 'conf_form_builder_forms';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            
            $table->boolean('active')->default(1);
            $table->bigInteger('sort_order');
            

        });
        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->boolean('active')->default(1);
                $table->bigInteger('sort_order');
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
            $table->dropColumn('active');
            $table->dropColumn('sort_order');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('active');
                $table->dropColumn('sort_order');
            });

            Schema::connection(config('database.archive_connection'))->dropIfExists(self::TABLE);
        }
    }
}
