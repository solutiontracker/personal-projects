<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfMailingListSubscriberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_mailing_list_subscriber';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('mailing_list_id')->default(0)->index('mailing_list_id');
            $table->integer('organizer_id')->default(0)->index('organizer_id');
            $table->string('email', 500)->index('email');
            $table->string('first_name', 500)->nullable();
            $table->string('last_name', 500)->nullable();
            $table->dateTime('unsubscribed')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->tinyInteger('is_checked')->nullable()->default(0)->index('is_checked');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('mailing_list_id')->default(0)->index('mailing_list_id');
                $table->integer('organizer_id')->default(0)->index('organizer_id');
                $table->string('email', 500)->index('email');
                $table->string('first_name', 500)->nullable();
                $table->string('last_name', 500)->nullable();
                $table->dateTime('unsubscribed')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->tinyInteger('is_checked')->nullable()->default(0)->index('is_checked');
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
