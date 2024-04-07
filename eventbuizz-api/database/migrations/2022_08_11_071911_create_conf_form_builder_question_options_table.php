<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfFormBuilderQuestionOptionsTable extends Migration
{
    const TABLE = 'conf_form_builder_question_options';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->foreignId("question_id")->index();
            $table->boolean("add_other")->nullable()->default(null);
            $table->boolean("description_visible")->nullable()->default(null);
            $table->boolean("response_validation")->nullable()->default(null);
            $table->boolean("section_based")->nullable()->default(null);
            $table->boolean("limit")->nullable()->default(null);
            $table->boolean("shuffle")->nullable()->default(null);
            $table->boolean("date")->nullable()->default(null);
            $table->boolean("time")->nullable()->default(null);
            $table->boolean("year")->nullable()->default(null);
            $table->integer("min")->nullable()->default(null);
            $table->integer("max")->nullable()->default(null);
            $table->string("time_type")->nullable()->default(null);
            $table->string("min_label")->nullable()->default(null);
            $table->string("max_label")->nullable()->default(null);
            $table->timestamps();
            $table->softDeletes();

        });
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->id();
                $table->foreignId("question_id")->index();
                $table->boolean("add_other")->nullable()->default(null);
                $table->boolean("description_visible")->nullable()->default(null);
                $table->boolean("response_validation")->nullable()->default(null);
                $table->boolean("section_based")->nullable()->default(null);
                $table->boolean("limit")->nullable()->default(null);
                $table->boolean("shuffle")->nullable()->default(null);
                $table->boolean("date")->nullable()->default(null);
                $table->boolean("time")->nullable()->default(null);
                $table->boolean("year")->nullable()->default(null);
                $table->integer("min")->nullable()->default(null);
                $table->integer("max")->nullable()->default(null);
                $table->string("time_type")->nullable()->default(null);
                $table->string("min_label")->nullable()->default(null);
                $table->string("max_label")->nullable()->default(null);
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
