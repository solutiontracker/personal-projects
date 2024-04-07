<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfEventNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    const TABLE = 'conf_event_news';

    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->bigInteger('id', true);
            $table->bigInteger('event_id')->nullable()->index('event_id');
            $table->text('title')->nullable();
            $table->longText('body')->nullable();
            $table->text('image')->nullable();
            $table->enum('status', ['draft', 'schedule', 'publish'])->nullable()->default('draft')->index('status');
            $table->dateTime('scheduled_at')->nullable()->index('scheduled_at');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id');
                $table->bigInteger('event_id')->nullable()->index('event_id');
                $table->text('title')->nullable();
                $table->longText('body')->nullable();
                $table->text('image')->nullable();
                $table->enum('status', ['draft', 'schedule', 'publish'])->nullable()->default('draft')->index('status');
                $table->dateTime('scheduled_at')->nullable()->index('scheduled_at');
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
