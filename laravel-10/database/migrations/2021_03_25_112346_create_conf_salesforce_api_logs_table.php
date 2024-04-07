<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfSalesforceApiLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_salesforce_api_logs';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('organizer_id')->nullable()->index('organizer_id');
            $table->unsignedBigInteger('attendee_id')->nullable()->index('attendee_id');
            $table->string('object', 50)->nullable();
            $table->string('action', 50)->nullable()->index('action');
            $table->text('response')->nullable();
            $table->integer('status_code')->nullable()->index('status_code');
            $table->tinyInteger('success')->nullable()->index('success');
            $table->timestamps();
            $table->text('input')->nullable();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->unsignedBigInteger('organizer_id')->nullable()->index('organizer_id');
                $table->unsignedBigInteger('attendee_id')->nullable()->index('attendee_id');
                $table->string('object', 50)->nullable();
                $table->string('action', 50)->nullable()->index('action');
                $table->text('response')->nullable();
                $table->integer('status_code')->nullable()->index('status_code');
                $table->tinyInteger('success')->nullable()->index('success');
                $table->timestamps();
                $table->text('input')->nullable();
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
