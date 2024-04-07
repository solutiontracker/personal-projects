<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventSubRegistrationQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_sub_registration_questions';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->enum('question_type', ['single', 'multiple', 'open', 'number', 'date', 'date_time', 'dropdown', 'matrix'])->index('question_type');
            $table->enum('required_question', ['1', '0'])->default('0');
            $table->enum('enable_comments', ['1', '0'])->default('0');
            $table->integer('sort_order')->index('sort_order');
            $table->bigInteger('sub_registration_id')->index('sub_registration_id');
            $table->tinyInteger('status')->index('status');
            $table->enum('link_to', ['0', '1', '2'])->default('0');
            $table->integer('max_options');
            $table->integer('min_options')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->enum('question_type', ['single', 'multiple', 'open', 'number', 'date', 'date_time', 'dropdown', 'matrix'])->index('question_type');
                $table->enum('required_question', ['1', '0'])->default('0');
                $table->enum('enable_comments', ['1', '0'])->default('0');
                $table->integer('sort_order')->index('sort_order');
                $table->bigInteger('sub_registration_id')->index('sub_registration_id');
                $table->tinyInteger('status')->index('status');
                $table->enum('link_to', ['0', '1', '2'])->default('0');
                $table->integer('max_options');
                $table->integer('min_options')->default(0);
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
