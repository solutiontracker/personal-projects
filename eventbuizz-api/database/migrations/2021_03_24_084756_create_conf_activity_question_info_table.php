<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfActivityQuestionInfoTable extends Migration
{
    const TABLE = "conf_activity_question_info";
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->index('name');
            $table->string('value')->index('value');
            $table->bigInteger('question_id')->index('question_id');
            $table->integer('language_id')->index('language_id');
            $table->tinyInteger('status')->index('status');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {
            Schema::connection('mysql_archive')->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->string('name');
                $table->string('value');
                $table->bigInteger('question_id');
                $table->integer('language_id');
                $table->tinyInteger('status');
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
