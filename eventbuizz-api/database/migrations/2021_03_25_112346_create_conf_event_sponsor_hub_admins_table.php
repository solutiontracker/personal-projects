<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventSponsorHubAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_sponsor_hub_admins';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('hub_admin_id');
            $table->integer('sponsor_id');
            $table->integer('event_id');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('hub_admin_id');
                $table->integer('sponsor_id');
                $table->integer('event_id');
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
