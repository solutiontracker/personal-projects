<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfOrganizerUserPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_organizer_user_permissions';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('organizer_user_id')->index('organizer_user_id');
            $table->integer('permission_id')->index('permission_id');
            $table->tinyInteger('add_permissions')->default(1);
            $table->tinyInteger('edit_permissions')->default(1);
            $table->tinyInteger('delete_permissions')->default(1);
            $table->tinyInteger('view_permissions')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('organizer_user_id')->index('organizer_user_id');
                $table->integer('permission_id')->index('permission_id');
                $table->tinyInteger('add_permissions')->default(1);
                $table->tinyInteger('edit_permissions')->default(1);
                $table->tinyInteger('delete_permissions')->default(1);
                $table->tinyInteger('view_permissions')->default(1);
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
