<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDepartmentOrganizationToConfPrintSettings extends Migration
{
    const TABLE = 'conf_print_settings';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('department')->nullable();
            $table->string('organization')->nullable();
            $table->string('industry')->nullable();
            $table->string('job_task')->nullable();
            $table->string('interest')->nullable();
            $table->string('network_group')->nullable();
            $table->string('delegate_number')->nullable();
            $table->string('table_number')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('department')->nullable();
                $table->string('organization')->nullable();
                $table->string('industry')->nullable();
                $table->string('job_task')->nullable();
                $table->string('interest')->nullable();
                $table->string('network_group')->nullable();
                $table->string('delegate_number')->nullable();
                $table->string('table_number')->nullable();
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
            $table->dropColumn('department');
            $table->dropColumn('organization');
            $table->dropColumn('industry');
            $table->dropColumn('job_task');
            $table->dropColumn('interest');
            $table->dropColumn('network_group');
            $table->dropColumn('delegate_number');
            $table->dropColumn('table_number');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('department');
                $table->dropColumn('organization');
                $table->dropColumn('industry');
                $table->dropColumn('job_task');
                $table->dropColumn('interest');
                $table->dropColumn('network_group');
                $table->dropColumn('delegate_number');
                $table->dropColumn('table_number');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
