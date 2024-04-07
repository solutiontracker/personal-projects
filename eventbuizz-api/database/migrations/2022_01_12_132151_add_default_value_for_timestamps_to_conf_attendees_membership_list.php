<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultValueForTimestampsToConfAttendeesMembershipList extends Migration
{
    const TABLE = 'conf_attendees_membership_list';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dateTime('created_at')->default(now())->change();
            $table->dateTime('updated_at')->default(now())->change();
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dateTime('created_at')->default(now())->change();
                $table->dateTime('updated_at')->default(now())->change();
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dateTime('created_at')->change();
            $table->dateTime('updated_at')->change();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dateTime('created_at')->change();
                $table->dateTime('updated_at')->change();
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
