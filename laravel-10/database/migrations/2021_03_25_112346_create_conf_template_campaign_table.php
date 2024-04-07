<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfTemplateCampaignTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_template_campaign';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('parent_id')->default(0);
            $table->integer('event_id');
            $table->integer('organizer_id');
            $table->string('subject', 500);
            $table->string('list_type');
            $table->integer('template_id');
            $table->bigInteger('l_t_id')->nullable();
            $table->string('l_t_type', 100)->nullable();
            $table->text('template')->nullable();
            $table->enum('status', ['draft', 'schedule', 'send_now'])->nullable();
            $table->date('schedule_date')->nullable()->default('0000-00-00');
            $table->time('schedule_time')->nullable()->default('00:00:00');
            $table->dateTime('sent_datetime')->nullable()->default('0000-00-00 00:00:00');
            $table->dateTime('utc_datetime')->nullable()->default('0000-00-00 00:00:00');
            $table->enum('schedule_repeat', ['1', '0'])->default('0');
            $table->integer('repeat_every_qty')->nullable();
            $table->enum('repeat_every_type', ['daily', 'weekly', 'monthly', 'yearly'])->nullable();
            $table->string('repeat_every_on')->nullable();
            $table->enum('end_type', ['never', 'on', 'after'])->nullable();
            $table->timestamp('end_on')->nullable();
            $table->integer('end_after')->nullable();
            $table->integer('in_progress')->default(0);
            $table->integer('send')->default(0);
            $table->integer('deferral')->default(0);
            $table->integer('hard_bounce')->default(0);
            $table->integer('soft_bounce')->default(0);
            $table->integer('open')->default(0);
            $table->integer('click')->default(0);
            $table->integer('reject')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('parent_id')->default(0);
                $table->integer('event_id');
                $table->integer('organizer_id');
                $table->string('subject', 500);
                $table->string('list_type');
                $table->integer('template_id');
                $table->bigInteger('l_t_id')->nullable();
                $table->string('l_t_type', 100)->nullable();
                $table->text('template')->nullable();
                $table->enum('status', ['draft', 'schedule', 'send_now'])->nullable();
                $table->date('schedule_date')->nullable()->default('0000-00-00');
                $table->time('schedule_time')->nullable()->default('00:00:00');
                $table->dateTime('sent_datetime')->nullable()->default('0000-00-00 00:00:00');
                $table->dateTime('utc_datetime')->nullable()->default('0000-00-00 00:00:00');
                $table->enum('schedule_repeat', ['1', '0'])->default('0');
                $table->integer('repeat_every_qty')->nullable();
                $table->enum('repeat_every_type', ['daily', 'weekly', 'monthly', 'yearly'])->nullable();
                $table->string('repeat_every_on')->nullable();
                $table->enum('end_type', ['never', 'on', 'after'])->nullable();
                $table->timestamp('end_on')->nullable();
                $table->integer('end_after')->nullable();
                $table->integer('in_progress')->default(0);
                $table->integer('send')->default(0);
                $table->integer('deferral')->default(0);
                $table->integer('hard_bounce')->default(0);
                $table->integer('soft_bounce')->default(0);
                $table->integer('open')->default(0);
                $table->integer('click')->default(0);
                $table->integer('reject')->default(0);
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
