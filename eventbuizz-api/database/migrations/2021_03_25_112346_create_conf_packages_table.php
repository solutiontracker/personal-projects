<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_packages';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('admin_id')->index('admin_id');
            $table->string('name');
            $table->text('description');
            $table->enum('no_of_event', ['Single', 'Unlimited']);
            $table->integer('total_attendees');
            $table->tinyInteger('registration_site_check');
            $table->integer('expire_duration');
            $table->enum('status', ['y', 'n'])->default('y')->index('status');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('admin_id')->index('admin_id');
                $table->string('name');
                $table->text('description');
                $table->enum('no_of_event', ['Single', 'Unlimited']);
                $table->integer('total_attendees');
                $table->tinyInteger('registration_site_check');
                $table->integer('expire_duration');
                $table->enum('status', ['y', 'n'])->default('y')->index('status');
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
