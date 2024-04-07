<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfPackagePaymentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_package_payment';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('admin_id')->index('admin_id');
            $table->bigInteger('customer_agent_id')->index('customer_agent_id');
            $table->bigInteger('organizer_id')->index('organizer_id');
            $table->bigInteger('assign_package_id')->index('assign_package_id');
            $table->string('invoice');
            $table->integer('amount');
            $table->dateTime('invoice_date');
            $table->bigInteger('sale_agent_id')->index('sale_agent_id');
            $table->string('contact_person');
            $table->string('contact_person_email');
            $table->string('contact_person_mobile', 25);
            $table->string('im_type');
            $table->string('im_id');
            $table->dateTime('first_contact_date');
            $table->dateTime('traning_session_date');
            $table->text('description');
            $table->string('currency');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->bigInteger('admin_id')->index('admin_id');
                $table->bigInteger('customer_agent_id')->index('customer_agent_id');
                $table->bigInteger('organizer_id')->index('organizer_id');
                $table->bigInteger('assign_package_id')->index('assign_package_id');
                $table->string('invoice');
                $table->integer('amount');
                $table->dateTime('invoice_date');
                $table->bigInteger('sale_agent_id')->index('sale_agent_id');
                $table->string('contact_person');
                $table->string('contact_person_email');
                $table->string('contact_person_mobile', 25);
                $table->string('im_type');
                $table->string('im_id');
                $table->dateTime('first_contact_date');
                $table->dateTime('traning_session_date');
                $table->text('description');
                $table->string('currency');
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
