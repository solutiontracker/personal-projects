<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfFormBuilderQuestionsTable extends Migration
{
    const TABLE = 'conf_form_builder_questions';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_builder_form_id')->index();
            $table->foreignId('form_builder_section_id')->index();
            $table->string('type');
            $table->boolean('required');
            $table->bigInteger('sort_order');
            $table->timestamps();
            $table->softDeletes();

        });
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->id();
                $table->foreignId('form_builder_form_id');
                $table->foreignId('form_builder_section_id');
                $table->string('type');
                $table->boolean('required');
                $table->bigInteger('sort_order');
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
