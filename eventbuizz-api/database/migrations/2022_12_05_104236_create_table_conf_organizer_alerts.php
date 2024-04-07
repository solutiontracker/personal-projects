<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableConfOrganizerAlerts extends Migration
{
    const TABLE = 'conf_organizer_alerts';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('pre_schedule')->default(0)->comment('0=no; 1=yes');
            $table->text('title');
            $table->text('description')->nullable();
            $table->time('time');
            $table->date('date');
            $table->enum('send_to',['all','native_app','white_label','individuals']);
            $table->tinyInteger('send_by_email')->default(1)->comment('0=no; 1=yes');
            $table->tinyInteger('send_by_notification')->default(0)->comment('0=no; 1=yes');
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->tinyInteger('pre_schedule')->default(0)->comment('0=no; 1=yes');
                $table->text('title');
                $table->text('description')->nullable();
                $table->time('time');
                $table->date('date');
                $table->enum('send_to',['all','native_app','white_label','individuals']);
                $table->tinyInteger('send_by_email')->default(1)->comment('0=no; 1=yes');
                $table->tinyInteger('send_by_notification')->default(0)->comment('0=no; 1=yes');
                $table->tinyInteger('status')->default(0);
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
