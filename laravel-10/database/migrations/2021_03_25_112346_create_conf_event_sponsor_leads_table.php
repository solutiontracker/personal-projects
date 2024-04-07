<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventSponsorLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_sponsor_leads';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('organizer_id');
            $table->bigInteger('event_id');
            $table->bigInteger('sponsor_id');
            $table->bigInteger('contact_person_id');
            $table->bigInteger('attendee_id');
            $table->text('notes');
            $table->string('image_file');
            $table->tinyInteger('permission_allowed')->default(0);
            $table->string('rating_star')->nullable();
            $table->dateTime('date_time');
            $table->timestamps();
            $table->softDeletes();
            $table->longText('term_text');
            $table->string('initial');
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('organizer_id');
                $table->bigInteger('event_id');
                $table->bigInteger('sponsor_id');
                $table->bigInteger('contact_person_id');
                $table->bigInteger('attendee_id');
                $table->text('notes');
                $table->string('image_file');
                $table->tinyInteger('permission_allowed')->default(0);
                $table->string('rating_star')->nullable();
                $table->dateTime('date_time');
                $table->timestamps();
                $table->softDeletes();
                $table->longText('term_text');
                $table->string('initial');
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
