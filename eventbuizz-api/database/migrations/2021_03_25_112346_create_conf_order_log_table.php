<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfOrderLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_order_log';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->string('log_type');
            $table->string('attach_file');
            $table->dateTime('log_due_date');
            $table->enum('is_resolved', ['y', 'n'])->default('n');
            $table->dateTime('marked_date');
            $table->text('comments');
            $table->bigInteger('admin_id');
            $table->bigInteger('order_id');
            $table->timestamps();
            $table->softDeletes();
            $table->dateTime('log_date');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->string('log_type');
                $table->string('attach_file');
                $table->dateTime('log_due_date');
                $table->enum('is_resolved', ['y', 'n'])->default('n');
                $table->dateTime('marked_date');
                $table->text('comments');
                $table->bigInteger('admin_id');
                $table->bigInteger('order_id');
                $table->timestamps();
            $table->softDeletes();
                $table->dateTime('log_date');
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
