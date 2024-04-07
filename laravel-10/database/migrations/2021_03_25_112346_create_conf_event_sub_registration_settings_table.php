<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventSubRegistrationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_sub_registration_settings';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->bigInteger('event_id')->index('event_id');
            $table->tinyInteger('listing')->default(0);
            $table->tinyInteger('answer')->default(0);
            $table->tinyInteger('link_to')->default(0);
            $table->tinyInteger('show_optional')->default(1);
            $table->tinyInteger('update_answer_email')->default(1);
            $table->tinyInteger('result_email')->default(0);
            $table->dateTime('end_date');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->bigInteger('event_id')->index('event_id');
                $table->tinyInteger('listing')->default(0);
                $table->tinyInteger('answer')->default(0);
                $table->tinyInteger('link_to')->default(0);
                $table->tinyInteger('show_optional')->default(1);
                $table->tinyInteger('update_answer_email')->default(1);
                $table->tinyInteger('result_email')->default(0);
                $table->dateTime('end_date');
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
