<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class AddTagContactPersonToConfEvents extends Migration
{
    const TABLE = 'conf_events';

    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->text('tags')->nullable();
            $table->string('contact_person_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->text('tags')->nullable();
                $table->string('contact_person_name')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }

    public function down()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('tags');
            $table->dropColumn('contact_person_name');
            $table->dropColumn('phone');
            $table->dropColumn('email');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('tags');
                $table->dropColumn('contact_person_name');
                $table->dropColumn('phone');
                $table->dropColumn('email');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
