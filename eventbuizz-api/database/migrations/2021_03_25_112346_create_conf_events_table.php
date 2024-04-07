<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_events';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->string('organizer_name');
            $table->string('name');
            $table->string('url')->index('url');
            $table->string('tickets_left');
            $table->date('start_date')->index('start_date');
            $table->date('end_date')->index('end_date');
            $table->time('start_time')->index('start_time');
            $table->time('end_time');
            $table->dateTime('cancellation_date')->index('cancellation_date');
            $table->dateTime('registration_end_date')->index('registration_end_date');
            $table->bigInteger('organizer_id')->index('organizer_id');
            $table->tinyInteger('status')->index('status');
            $table->timestamps();
            $table->softDeletes();
            $table->tinyInteger('is_updated_label')->default(0);
            $table->integer('language_id')->index('language_id');
            $table->integer('timezone_id')->index('timezone_id');
            $table->integer('country_id')->index('country_id');
            $table->integer('office_country_id')->index('office_country_id');
            $table->bigInteger('owner_id')->index('owner_id');
            $table->enum('export_setting', [';', ','])->default(';');
            $table->tinyInteger('show_native_app_link')->default(0);
            $table->tinyInteger('organizer_site')->default(1);
            $table->dateTime('native_app_acessed_date')->nullable();
            $table->string('native_app_timer')->default('1800');
            $table->string('white_label_sender_name')->nullable();
            $table->string('white_label_sender_email')->nullable();
            $table->tinyInteger('enable_cache')->default(0);
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->tinyInteger('is_template')->default(0);
            $table->tinyInteger('is_advance_template')->default(0);
            $table->tinyInteger('is_wizard_template')->default(0);
            $table->tinyInteger('type')->default(0);
            $table->tinyInteger('is_registration')->default(0);
            $table->tinyInteger('is_app')->default(0);
            $table->tinyInteger('is_map')->nullable()->default(0);
            $table->integer('template_id')->nullable();
            $table->integer('end_event_total_attendee_count')->nullable();
            $table->tinyInteger('allow_all_qualities')->default(0);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->string('organizer_name');
                $table->string('name');
                $table->string('url')->index('url');
                $table->string('tickets_left');
                $table->date('start_date')->index('start_date');
                $table->date('end_date')->index('end_date');
                $table->time('start_time')->index('start_time');
                $table->time('end_time');
                $table->dateTime('cancellation_date')->index('cancellation_date');
                $table->dateTime('registration_end_date')->index('registration_end_date');
                $table->bigInteger('organizer_id')->index('organizer_id');
                $table->tinyInteger('status')->index('status');
                $table->timestamps();
            $table->softDeletes();
                $table->tinyInteger('is_updated_label')->default(0);
                $table->integer('language_id')->index('language_id');
                $table->integer('timezone_id')->index('timezone_id');
                $table->integer('country_id')->index('country_id');
                $table->integer('office_country_id')->index('office_country_id');
                $table->bigInteger('owner_id')->index('owner_id');
                $table->enum('export_setting', [';', ','])->default(';');
                $table->tinyInteger('show_native_app_link')->default(0);
                $table->tinyInteger('organizer_site')->default(1);
                $table->dateTime('native_app_acessed_date')->nullable();
                $table->string('native_app_timer')->default('1800');
                $table->string('white_label_sender_name')->nullable();
                $table->string('white_label_sender_email')->nullable();
                $table->tinyInteger('enable_cache')->default(0);
                $table->string('latitude')->nullable();
                $table->string('longitude')->nullable();
                $table->tinyInteger('is_template')->default(0);
                $table->tinyInteger('is_advance_template')->default(0);
                $table->tinyInteger('is_wizard_template')->default(0);
                $table->tinyInteger('type')->default(0);
                $table->tinyInteger('is_registration')->default(0);
                $table->tinyInteger('is_app')->default(0);
                $table->tinyInteger('is_map')->nullable()->default(0);
                $table->integer('template_id')->nullable();
                $table->integer('end_event_total_attendee_count')->nullable();
                $table->tinyInteger('allow_all_qualities')->default(0);
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
