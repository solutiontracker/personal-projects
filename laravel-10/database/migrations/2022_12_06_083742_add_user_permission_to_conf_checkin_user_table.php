<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserPermissionToConfCheckinUserTable extends Migration
{
   
 const TABLE = 'conf_checkin_user';
 /**
  * Run the migrations.
  *
  * @return void
  */
 public function up()
 {
    Schema::table(self::TABLE, function (Blueprint $table) {
        $table->string("permission",255)->nullable();
        $table->string("program",255)->nullable();
        $table->string("group",255)->nullable();
        $table->string("ticket",255)->nullable();
    });

    if (app()->environment('live')) {
        Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
            $table->string("permission",255)->nullable();
            $table->string("program",255)->nullable();
            $table->string("group",255)->nullable();
            $table->string("ticket",255)->nullable();
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
        $table->dropColumn('permission');
        $table->dropColumn('program');
        $table->dropColumn('group');
        $table->dropColumn('ticket');
    });

    if (app()->environment('live')) {
        Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('permission');
            $table->dropColumn('program');
            $table->dropColumn('group');
            $table->dropColumn('ticket');
        });
        EBSchema::createBeforeDeleteTrigger(self::TABLE);
    }
 }
}
