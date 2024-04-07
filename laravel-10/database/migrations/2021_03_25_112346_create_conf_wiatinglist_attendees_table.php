<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfWiatinglistAttendeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_wiatinglist_attendees';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('event_id')->index('event_id');
            $table->bigInteger('order_id')->index('order_id');
            $table->bigInteger('attendee_id')->index('attendee_id');
            $table->integer('status')->index('status')->comment('0=Pending,1=Sent,2=Attending,3=Not Interested,4=expired');
            $table->text('order_data');
            $table->integer('type')->default(1)->index('type')->comment('1=Waiting List,2=Mister Tango');
            $table->dateTime('date_sent')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('order_id')->index('order_id');
                $table->bigInteger('attendee_id')->index('attendee_id');
                $table->integer('status')->index('status')->comment('0=Pending,1=Sent,2=Attending,3=Not Interested,4=expired');
                $table->text('order_data');
                $table->integer('type')->default(1)->index('type')->comment('1=Waiting List,2=Mister Tango');
                $table->dateTime('date_sent')->nullable();
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
