<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfEmailLogTable extends Migration
{
    const TABLE = 'conf_email_log';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('event_id')->index('event_id')->nullable();
            $table->string('to', 100)->nullable();
            $table->string('from', 100)->nullable();
            $table->string('cc', 100)->nullable();
            $table->string('bcc', 100)->nullable();
            $table->string('subject', 255)->nullable();
            $table->string('template', 255)->nullable();
            $table->text('headers')->nullable();
            $table->longText('body')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('event_id')->index('event_id')->nullable();
                $table->string('to', 100)->nullable();
                $table->string('from', 100)->nullable();
                $table->string('cc', 100)->nullable();
                $table->string('bcc', 100)->nullable();
                $table->string('subject', 255)->nullable();
                $table->string('template', 255)->nullable();
                $table->text('headers')->nullable();
                $table->longText('body')->nullable();
                $table->timestamps();
                $table->softDeletes();
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
        EBSchema::dropDeleteTrigger(self::TABLE);
        Schema::dropIfExists(self::TABLE);
        Schema::connection(config('database.archive_connection'))->dropIfExists(self::TABLE);
    }
}
