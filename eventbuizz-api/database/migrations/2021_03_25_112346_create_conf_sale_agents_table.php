<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfSaleAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_sale_agents';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('admin_id');
            $table->bigInteger('organizer_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->integer('send_password');
            $table->string('title');
            $table->string('image');
            $table->string('company');
            $table->string('password');
            $table->string('phone', 25);
            $table->string('address');
            $table->enum('status', ['y', 'n'])->default('y');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('admin_id');
                $table->bigInteger('organizer_id');
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email');
                $table->integer('send_password');
                $table->string('title');
                $table->string('image');
                $table->string('company');
                $table->string('password');
                $table->string('phone', 25);
                $table->string('address');
                $table->enum('status', ['y', 'n'])->default('y');
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
