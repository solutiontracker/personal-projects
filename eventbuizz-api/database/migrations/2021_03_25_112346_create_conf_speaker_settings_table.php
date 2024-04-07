<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfSpeakerSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_speaker_settings';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->bigInteger('event_id')->index('event_id');
            $table->tinyInteger('phone')->default(0);
            $table->tinyInteger('email')->default(0);
            $table->tinyInteger('title')->default(1);
            $table->tinyInteger('department')->default(1);
            $table->tinyInteger('company_name')->default(1);
            $table->tinyInteger('show_country')->default(0);
            $table->tinyInteger('contact_vcf')->default(0);
            $table->tinyInteger('program')->default(1);
            $table->tinyInteger('category_group')->default(1);
            $table->tinyInteger('show_group')->default(1);
            $table->tinyInteger('show_document')->default(1);
            $table->tinyInteger('group')->default(1);
            $table->tinyInteger('initial')->default(1);
            $table->tinyInteger('chat')->default(0);
            $table->tinyInteger('hide_attendee')->default(0);
            $table->tinyInteger('tab');
            $table->enum('default_display', ['name', 'group'])->default('name');
            $table->enum('order_by', ['first_name', 'last_name', 'custom'])->default('last_name');
            $table->tinyInteger('registration_site_limit')->default(4);
            $table->tinyInteger('poll')->default(0);
            $table->tinyInteger('document')->default(0);
            $table->tinyInteger('delegate_number')->default(1);
            $table->tinyInteger('network_group')->default(1);
            $table->tinyInteger('table_number')->default(1);
            $table->tinyInteger('organization')->default(1);
            $table->tinyInteger('interest')->default(1);
            $table->tinyInteger('bio_info')->default(1);
            $table->tinyInteger('show_custom_field')->default(0);
            $table->tinyInteger('show_industry')->default(1);
            $table->tinyInteger('show_job_tasks')->default(1);
            $table->tinyInteger('gdpr_accepted')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->bigInteger('event_id')->index('event_id');
                $table->tinyInteger('phone')->default(0);
                $table->tinyInteger('email')->default(0);
                $table->tinyInteger('title')->default(1);
                $table->tinyInteger('department')->default(1);
                $table->tinyInteger('company_name')->default(1);
                $table->tinyInteger('show_country')->default(0);
                $table->tinyInteger('contact_vcf')->default(0);
                $table->tinyInteger('program')->default(1);
                $table->tinyInteger('category_group')->default(1);
                $table->tinyInteger('show_group')->default(1);
                $table->tinyInteger('show_document')->default(1);
                $table->tinyInteger('group')->default(1);
                $table->tinyInteger('initial')->default(1);
                $table->tinyInteger('chat')->default(0);
                $table->tinyInteger('hide_attendee')->default(0);
                $table->tinyInteger('tab');
                $table->enum('default_display', ['name', 'group'])->default('name');
                $table->enum('order_by', ['first_name', 'last_name', 'custom'])->default('last_name');
                $table->tinyInteger('registration_site_limit')->default(4);
                $table->tinyInteger('poll')->default(0);
                $table->tinyInteger('document')->default(0);
                $table->tinyInteger('delegate_number')->default(1);
                $table->tinyInteger('network_group')->default(1);
                $table->tinyInteger('table_number')->default(1);
                $table->tinyInteger('organization')->default(1);
                $table->tinyInteger('interest')->default(1);
                $table->tinyInteger('bio_info')->default(1);
                $table->tinyInteger('show_custom_field')->default(0);
                $table->tinyInteger('show_industry')->default(1);
                $table->tinyInteger('show_job_tasks')->default(1);
                $table->tinyInteger('gdpr_accepted')->default(1);
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
