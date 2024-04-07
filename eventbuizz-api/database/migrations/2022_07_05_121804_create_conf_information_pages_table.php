<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfInformationPagesTable extends Migration
{
    const TABLE = 'conf_information_pages';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->unsignedBigInteger('section_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->string('image')->nullable();
            $table->string('url')->nullable();
            $table->string('icon')->nullable();
            $table->string('website_protocol')->nullable();
            $table->string('image_position')->nullable();
            $table->string('pdf')->nullable();
            $table->tinyInteger('page_type')->comment('1=section,2=page,3=link');
            $table->enum('target',['', '_blank', '_top', '_self', '_parent']);
            $table->softDeletes();
            $table->timestamps();
        });
        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('event_id');
                $table->unsignedBigInteger('section_id');
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->integer('sort_order')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->string('image')->nullable();
                $table->string('url')->nullable();
                $table->string('icon')->nullable();
                $table->string('website_protocol')->nullable();
                $table->string('image_position')->nullable();
                $table->string('pdf')->nullable();
                $table->tinyInteger('page_type')->comment('1=section,2=page,3=link');
                $table->enum('target',['', '_blank', '_top', '_self', '_parent']);
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
