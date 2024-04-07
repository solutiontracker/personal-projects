<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfOrganizerAccessRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_organizer_access_request';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('organizer_id')->nullable()->index('organizer_id');
            $table->integer('admin_id')->nullable()->index('admin_id');
            $table->integer('sub_organizer_id')->nullable()->index('sub_organizer_id');
            $table->string('ip', 30)->nullable();
            $table->tinyInteger('status')->nullable()->default(0)->index('status');
            $table->string('token', 10)->nullable();
            $table->dateTime('expire_at')->nullable();
            $table->string('type', 5)->nullable();
            $table->string('to', 50)->nullable();
            $table->tinyInteger('request_type')->default(0)->index('request_type');
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('email', 100)->nullable();
            $table->string('phone', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('organizer_id')->nullable()->index('organizer_id');
                $table->integer('admin_id')->nullable()->index('admin_id');
                $table->integer('sub_organizer_id')->nullable()->index('sub_organizer_id');
                $table->string('ip', 30)->nullable();
                $table->tinyInteger('status')->nullable()->default(0)->index('status');
                $table->string('token', 10)->nullable();
                $table->dateTime('expire_at')->nullable();
                $table->string('type', 5)->nullable();
                $table->string('to', 50)->nullable();
                $table->tinyInteger('request_type')->default(0)->index('request_type');
                $table->string('first_name', 100)->nullable();
                $table->string('last_name', 100);
                $table->string('email', 100)->nullable();
                $table->string('phone', 50)->nullable();
                $table->text('notes')->nullable();
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
