<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfPrintSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_print_settings';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('event_id')->index('event_id');
            $table->tinyInteger('active')->nullable()->index('active');
            $table->string('username');
            $table->string('password');
            $table->text('dropdown');
            $table->text('sub_category');
            $table->tinyInteger('auto_select_subcategory')->nullable();
            $table->tinyInteger('browser')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('event_id')->index('event_id');
                $table->tinyInteger('active')->nullable()->index('active');
                $table->string('username');
                $table->string('password');
                $table->text('dropdown');
                $table->text('sub_category');
                $table->tinyInteger('auto_select_subcategory')->nullable();
                $table->tinyInteger('browser')->nullable();
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
