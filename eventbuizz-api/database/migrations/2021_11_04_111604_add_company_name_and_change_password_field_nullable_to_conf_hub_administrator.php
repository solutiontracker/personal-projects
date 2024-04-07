<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyNameAndChangePasswordFieldNullableToConfHubAdministrator extends Migration
{
    const TABLE = 'conf_hub_administrator';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->string('company_name')->nullable();
            $table->string('password')->nullable()->change();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->string('company_name')->nullable();
                $table->string('password')->nullable()->change();
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('company_name');
            $table->string('password')->change();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('company_name');
                $table->string('password')->change();
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
