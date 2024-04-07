<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventUserLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_user_leads';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->bigInteger('event_id');
            $table->bigInteger('user_id');
            $table->string('device_id');
            $table->string('email');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->integer('rating')->nullable();
            $table->string('image_file')->nullable();
            $table->tinyInteger('permission_allowed')->default(0);
            $table->longText('raw_data')->nullable();
            $table->timestamp('lead_date')->useCurrent();
            $table->timestamps();
            $table->softDeletes();
            $table->longText('term_text')->nullable();
            $table->string('initial')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->bigInteger('event_id');
                $table->bigInteger('user_id');
                $table->string('device_id');
                $table->string('email');
                $table->string('first_name');
                $table->string('last_name')->nullable();
                $table->integer('rating')->nullable();
                $table->string('image_file')->nullable();
                $table->tinyInteger('permission_allowed')->default(0);
                $table->longText('raw_data')->nullable();
                $table->timestamp('lead_date')->useCurrent();
                $table->timestamps();
            $table->softDeletes();
                $table->longText('term_text')->nullable();
                $table->string('initial')->nullable();
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
