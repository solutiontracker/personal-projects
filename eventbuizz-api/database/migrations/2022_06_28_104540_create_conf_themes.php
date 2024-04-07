<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfThemes extends Migration
{
    const TABLE = 'conf_themes';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('status');
            $table->text('thumbnail');
            $table->text('demo_url');
            $table->boolean('is_paid');
            $table->string('price');
            $table->boolean('is_default');
            $table->string('slug');
            $table->timestamps();
            $table->softDeletes();

        });
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->boolean('status');
                $table->text('thumbnail');
                $table->text('demo_url');
                $table->boolean('is_paid');
                $table->string('price');
                $table->boolean('is_default');
                $table->string('slug');
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
