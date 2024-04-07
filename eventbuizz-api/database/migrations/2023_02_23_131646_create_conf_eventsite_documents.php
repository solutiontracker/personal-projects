<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventsiteDocuments extends Migration
{
    const TABLE = "conf_eventsite_documents";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("event_id");
            $table->unsignedBigInteger("registration_form_id");
            $table->unsignedBigInteger("file_size")->nullable();
            $table->string("file_name");

            $table->softDeletes();
            $table->timestamps();

            $table->index("event_id");
            $table->index("registration_form_id");
            $table->index("file_name");
            $table->integer("sort_order");
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger("event_id");
                $table->unsignedBigInteger("registration_form_id");
                $table->unsignedBigInteger("file_size");
                $table->string("file_name");

                $table->softDeletes();
                $table->timestamps();

                $table->index("event_id");
                $table->index("registration_form_id");
                $table->index("file_name");
                $table->integer("sort_order");
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
