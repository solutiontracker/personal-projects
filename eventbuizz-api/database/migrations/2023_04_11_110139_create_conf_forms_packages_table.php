<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfFormsPackagesTable extends Migration
{
    const TABLE = "conf_forms_packages";
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->index('event_id');
            $table->string('heading');
            $table->string('sub_heading');
            $table->string('price');
            $table->longText('description')->nullable()->default('');
            $table->tinyInteger('status')->default(1);
            $table->foreignId('registration_form_id')->index('registration_form_id');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->index('event_id');
            $table->string('heading');
            $table->string('sub_heading');
            $table->string('price');
            $table->longText('description')->nullable()->default('');
            $table->tinyInteger('status')->default(1);
            $table->foreignId('registration_form_id')->index('registration_form_id');
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
        Schema::dropIfExists(self::TABLE);
        EBSchema::dropDeleteTrigger(self::TABLE);
        Schema::connection(config('database.archive_connection'))->dropIfExists(self::TABLE);
    }


}
