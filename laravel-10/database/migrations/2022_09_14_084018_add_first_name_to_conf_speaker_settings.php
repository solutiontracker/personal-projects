<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFirstNameToConfSpeakerSettings extends Migration
{
    const TABLE = 'conf_speaker_settings';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('first_name_passport')->default(0);
            $table->tinyInteger('last_name_passport')->default(0);
            $table->tinyInteger('birth_date')->default(0);
            $table->tinyInteger('spoken_languages')->default(0);
            $table->tinyInteger('employment_date')->default(0);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('first_name_passport')->default(0);
                $table->tinyInteger('last_name_passport')->default(0);
                $table->tinyInteger('birth_date')->default(0);
                $table->tinyInteger('spoken_languages')->default(0);
                $table->tinyInteger('employment_date')->default(0);
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
        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->dropColumn('first_name_passport');
            $table->dropColumn('last_name_passport');
            $table->dropColumn('birth_date');
            $table->dropColumn('spoken_languages');
            $table->dropColumn('employment_date');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('first_name_passport');
                $table->dropColumn('last_name_passport');
                $table->dropColumn('birth_date');
                $table->dropColumn('spoken_languages');
                $table->dropColumn('employment_date');
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE);

        }
    }
}
