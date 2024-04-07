<?php

use Illuminate\Database\Migrations\Migration;
use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfOrganizerHubEmailTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_organizer_hub_email_template';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('organizer_id')->index('organizer_id');
            $table->string('alias');
            $table->string('title');
            $table->string('subject');
            $table->text('template');
            $table->longText('content');
            $table->tinyInteger('status');
            $table->tinyInteger('template_type')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('organizer_id')->index('organizer_id');
                $table->string('alias');
                $table->string('title');
                $table->string('subject');
                $table->text('template');
                $table->longText('content');
                $table->tinyInteger('status');
                $table->tinyInteger('template_type')->default(1);
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
