<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class KeyColumnsToConfEventBadgesDesign extends Migration
{
    const TABLE = 'conf_event_badges_design';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table(self::TABLE, function (Blueprint $table) {
            $table->tinyInteger('isMobileKey')->default(0);
            $table->text('mobileKeyLocation')->nullable();
            $table->tinyInteger('isMobileKey2')->default(0);
            $table->text('mobileKey2Location')->nullable();
            $table->tinyInteger('isMobileKey3')->default(0);
            $table->text('mobileKey3Location')->nullable();
            $table->tinyInteger('isMobileKey4')->default(0);
            $table->text('mobileKey4Location')->nullable();
            $table->tinyInteger('isMobileKey5')->default(0);
            $table->text('mobileKey5Location')->nullable();
            $table->tinyInteger('isMobileKey6')->default(0);
            $table->text('mobileKey6Location')->nullable();
            $table->tinyInteger('isMobileKey7')->default(0);
            $table->text('mobileKey7Location')->nullable();
            $table->tinyInteger('isMobileKey8')->default(0);
            $table->text('mobileKey8Location')->nullable();
            $table->tinyInteger('isMobileKey9')->default(0);
            $table->text('mobileKey9Location')->nullable();
            $table->tinyInteger('isMobileKey10')->default(0);
            $table->text('mobileKey10Location')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->tinyInteger('isMobileKey')->default(0);
                $table->text('mobileKeyLocation')->nullable();
                $table->tinyInteger('isMobileKey2')->default(0);
                $table->text('mobileKey2Location')->nullable();
                $table->tinyInteger('isMobileKey3')->default(0);
                $table->text('mobileKey3Location')->nullable();
                $table->tinyInteger('isMobileKey4')->default(0);
                $table->text('mobileKey4Location')->nullable();
                $table->tinyInteger('isMobileKey5')->default(0);
                $table->text('mobileKey5Location')->nullable();
                $table->tinyInteger('isMobileKey6')->default(0);
                $table->text('mobileKey6Location')->nullable();
                $table->tinyInteger('isMobileKey7')->default(0);
                $table->text('mobileKey7Location')->nullable();
                $table->tinyInteger('isMobileKey8')->default(0);
                $table->text('mobileKey8Location')->nullable();
                $table->tinyInteger('isMobileKey9')->default(0);
                $table->text('mobileKey9Location')->nullable();
                $table->tinyInteger('isMobileKey10')->default(0);
                $table->text('mobileKey10Location')->nullable();
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
            $table->dropColumn('isMobileKeyLocation');
            $table->dropColumn('mobileKeyLocation');
            $table->dropColumn('isMobileKey2Location');
            $table->dropColumn('mobileKey2Location');
            $table->dropColumn('isMobileKey3Location');
            $table->dropColumn('mobileKey3Location');
            $table->dropColumn('isMobileKey4Location');
            $table->dropColumn('mobileKey4Location');
            $table->dropColumn('isMobileKey5Location');
            $table->dropColumn('mobileKey5Location');
            $table->dropColumn('isMobileKey6Location');
            $table->dropColumn('mobileKey6Location');
            $table->dropColumn('isMobileKey7Location');
            $table->dropColumn('mobileKey7Location');
            $table->dropColumn('isMobileKey8Location');
            $table->dropColumn('mobileKey8Location');
            $table->dropColumn('isMobileKey9Location');
            $table->dropColumn('mobileKey9Location');
            $table->dropColumn('isMobileKey10Location');
            $table->dropColumn('mobileKey1Location0');
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->dropColumn('isMobileKeyLocation');
                $table->dropColumn('mobileKeyLocation');
                $table->dropColumn('isMobileKey2Location');
                $table->dropColumn('mobileKey2Location');
                $table->dropColumn('isMobileKey3Location');
                $table->dropColumn('mobileKey3Location');
                $table->dropColumn('isMobileKey4Location');
                $table->dropColumn('mobileKey4Location');
                $table->dropColumn('isMobileKey5Location');
                $table->dropColumn('mobileKey5Location');
                $table->dropColumn('isMobileKey6Location');
                $table->dropColumn('mobileKey6Location');
                $table->dropColumn('isMobileKey7Location');
                $table->dropColumn('mobileKey7Location');
                $table->dropColumn('isMobileKey8Location');
                $table->dropColumn('mobileKey8Location');
                $table->dropColumn('isMobileKey9Location');
                $table->dropColumn('mobileKey9Location');
                $table->dropColumn('isMobileKey10Location');
                $table->dropColumn('mobileKey1Location0');
            });
            EBSchema::createBeforeDeleteTrigger(self::TABLE);
        }
    }
}
