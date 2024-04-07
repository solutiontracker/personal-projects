<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfOrganizerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_organizer';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('parent_id')->default(0)->index('parent_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('user_name');
            $table->string('email')->index('email');
            $table->string('password');
            $table->string('phone', 55);
            $table->string('address');
            $table->string('house_number');
            $table->string('company');
            $table->string('vat_number', 55);
            $table->string('zip_code', 55);
            $table->string('city');
            $table->integer('country');
            $table->dateTime('create_date');
            $table->dateTime('expire_date');
            $table->string('domain');
            $table->bigInteger('total_space');
            $table->bigInteger('space_private_document');
            $table->integer('sub_admin_limit')->nullable()->default(0);
            $table->integer('plugnplay_sub_admin_limit')->nullable()->default(0);
            $table->enum('status', ['1', '2', '3'])->default('1')->index('status')->comment('1= Active, 2 = Pending, 3 = Expire');
            $table->enum('user_type', ['super', 'admin', 'demo', 'readonly'])->default('super')->index('user_type');
            $table->tinyInteger('internal_organizer')->default(0)->index('internal_organizer');
            $table->string('legal_contact_first_name')->nullable();
            $table->enum('export_setting', [';', ','])->default(';');
            $table->string('legal_contact_last_name')->nullable();
            $table->string('legal_contact_email')->nullable();
            $table->string('legal_contact_mobile', 55)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('remember_token', 250);
            $table->tinyInteger('show_native_app_link_all_events')->default(0);
            $table->integer('allow_native_app');
            $table->string('api_key')->index('api_key');
            $table->tinyInteger('allow_api')->default(0);
            $table->tinyInteger('allow_card_reader')->default(0);
            $table->tinyInteger('white_label_email')->default(0);
            $table->tinyInteger('authentication')->default(0);
            $table->tinyInteger('authentication_type')->default(1);
            $table->string('authentication_code');
            $table->tinyInteger('email_marketing_template')->default(0);
            $table->tinyInteger('mailing_list')->default(0);
            $table->tinyInteger('access_plug_play')->default(0);
            $table->dateTime('authentication_created_date');
            $table->dateTime('license_start_date');
            $table->dateTime('license_end_date');
            $table->enum('license_type', ['Professional', 'Basic']);
            $table->tinyInteger('paid');
            $table->tinyInteger('allow_admin_access')->default(1);
            $table->tinyInteger('allow_plug_and_play_access')->default(0);
            $table->tinyInteger('allow_nem_id')->default(0);
            $table->tinyInteger('eventbuizz_app')->default(0);
            $table->tinyInteger('white_label_app')->default(0);
            $table->enum('language_id', ['1', '2', '3', '4', '5', '6', '7', '8', '9'])->default('1');
            $table->tinyInteger('auto_renewal')->nullable()->default(1);
            $table->integer('notice_period');
            $table->string('owner');
            $table->string('contact_name');
            $table->string('contact_email');
            $table->text('notes');
            $table->timestamp('terminated_on')->default('0000-00-00 00:00:00');
            $table->string('last_login_ip', 30)->nullable();
            $table->text('token')->nullable();
            $table->dateTime('token_expire_at')->nullable();
            $table->tinyInteger('show_all_events')->default(0);
            $table->tinyInteger('crm_integrated')->default(0);
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('parent_id')->default(0)->index('parent_id');
                $table->string('first_name');
                $table->string('last_name');
                $table->string('user_name');
                $table->string('email')->index('email');
                $table->string('password');
                $table->string('phone', 55);
                $table->string('address');
                $table->string('house_number');
                $table->string('company');
                $table->string('vat_number', 55);
                $table->string('zip_code', 55);
                $table->string('city');
                $table->integer('country');
                $table->dateTime('create_date');
                $table->dateTime('expire_date');
                $table->string('domain');
                $table->bigInteger('total_space');
                $table->bigInteger('space_private_document');
                $table->integer('sub_admin_limit')->nullable()->default(0);
                $table->integer('plugnplay_sub_admin_limit')->nullable()->default(0);
                $table->enum('status', ['1', '2', '3'])->default('1')->index('status')->comment('1= Active, 2 = Pending, 3 = Expire');
                $table->enum('user_type', ['super', 'admin', 'demo', 'readonly'])->default('super')->index('user_type');
                $table->tinyInteger('internal_organizer')->default(0)->index('internal_organizer');
                $table->string('legal_contact_first_name')->nullable();
                $table->enum('export_setting', [';', ','])->default(';');
                $table->string('legal_contact_last_name')->nullable();
                $table->string('legal_contact_email')->nullable();
                $table->string('legal_contact_mobile', 55)->nullable();
                $table->timestamps();
            $table->softDeletes();
                $table->string('remember_token', 250);
                $table->tinyInteger('show_native_app_link_all_events')->default(0);
                $table->integer('allow_native_app');
                $table->string('api_key')->index('api_key');
                $table->tinyInteger('allow_api')->default(0);
                $table->tinyInteger('allow_card_reader')->default(0);
                $table->tinyInteger('white_label_email')->default(0);
                $table->tinyInteger('authentication')->default(0);
                $table->tinyInteger('authentication_type')->default(1);
                $table->string('authentication_code');
                $table->tinyInteger('email_marketing_template')->default(0);
                $table->tinyInteger('mailing_list')->default(0);
                $table->tinyInteger('access_plug_play')->default(0);
                $table->dateTime('authentication_created_date');
                $table->dateTime('license_start_date');
                $table->dateTime('license_end_date');
                $table->enum('license_type', ['Professional', 'Basic']);
                $table->tinyInteger('paid');
                $table->tinyInteger('allow_admin_access')->default(1);
                $table->tinyInteger('allow_plug_and_play_access')->default(0);
                $table->tinyInteger('allow_nem_id')->default(0);
                $table->tinyInteger('eventbuizz_app')->default(0);
                $table->tinyInteger('white_label_app')->default(0);
                $table->enum('language_id', ['1', '2', '3', '4', '5', '6', '7', '8', '9'])->default('1');
                $table->tinyInteger('auto_renewal')->nullable()->default(1);
                $table->integer('notice_period');
                $table->string('owner');
                $table->string('contact_name');
                $table->string('contact_email');
                $table->text('notes');
                $table->timestamp('terminated_on')->default('0000-00-00 00:00:00');
                $table->string('last_login_ip', 30)->nullable();
                $table->text('token')->nullable();
                $table->dateTime('token_expire_at')->nullable();
                $table->tinyInteger('show_all_events')->default(0);
                $table->tinyInteger('crm_integrated')->default(0);
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
