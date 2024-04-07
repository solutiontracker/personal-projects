<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventsiteDocumentTypes extends Migration
{
    /**
     * table name
     */
    const TABLE = "conf_eventsite_document_types";


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->string("name");

            $table->unsignedBigInteger("registration_form_id");
            $table->unsignedBigInteger("event_id");
            $table->boolean("is_required");

            $table->index("name");
            $table->index("registration_form_id");
            $table->index("event_id");
            $table->integer("sort_order");

            $table->softDeletes();
            $table->timestamps();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->table(self::TABLE, function (Blueprint $table) {
                $table->id();
                $table->string("name");

                $table->unsignedBigInteger("registration_form_id");
                $table->unsignedBigInteger("event_id");
                $table->boolean("is_required");

                $table->index("name");
                $table->index("registration_form_id");
                $table->index("event_id");
                $table->integer("sort_order");

                $table->softDeletes();
                $table->timestamps();
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
