<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfSubAdminLicenceAssignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_sub_admin_licence_assign';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('licence_id')->index('licence_id');
            $table->integer('organizer_id')->index('organizer_id');
            $table->tinyInteger('status')->default(1)->index('status');
            $table->timestamp('licence_start_date')->useCurrent();
            $table->timestamp('licence_end_date')->default('0000-00-00 00:00:00');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('licence_id')->index('licence_id');
                $table->integer('organizer_id')->index('organizer_id');
                $table->tinyInteger('status')->default(1)->index('status');
                $table->timestamp('licence_start_date')->useCurrent();
                $table->timestamp('licence_end_date')->default('0000-00-00 00:00:00');
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
