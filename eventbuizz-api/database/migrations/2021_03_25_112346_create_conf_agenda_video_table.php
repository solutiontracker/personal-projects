<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfAgendaVideoTable extends Migration
    {
        const TABLE = 'conf_agenda_video';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('name')->nullable();
                $table->enum('type', ['link', 'local', 'live', 'agora-realtime-broadcasting', 'agora-external-streaming', 'agora-rooms', 'agora-webinar', 'agora-panel-disscussions'])->nullable();
                $table->string('plateform', 100)->nullable();
                $table->string('size', 30)->nullable();
                $table->text('url')->nullable();
                $table->string('filename', 60)->nullable();
                $table->integer('agenda_id')->nullable()->index('agenda_id');
                $table->tinyInteger('status')->default(0);
                $table->tinyInteger('is_live')->default(0);
                $table->string('thumbnail', 100)->nullable();
                $table->tinyInteger('is_iframe')->nullable()->default(0);
                $table->tinyInteger('is_meeting')->default(0);
                $table->bigInteger('moderator')->nullable()->index('moderator');
                $table->bigInteger('broadcaster')->nullable()->default(0);
                $table->text('iframe_data')->nullable();
                $table->tinyInteger('broadcasting')->nullable()->default(0);
                $table->enum('broadcasting_type', ['eventbuizz', 'vimeo-youtube'])->nullable();
                $table->string('streaming_url')->nullable();
                $table->string('streaming_key')->nullable();
                $table->tinyInteger('private')->nullable()->default(1);
                $table->integer('sort')->default(0);
                $table->integer('sort_order')->nullable()->default(0);
                $table->string('archiveId', 150)->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            if (app()->environment('live')) {

                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->string('name')->nullable();
                    $table->enum('type', ['link', 'local', 'live', 'agora-realtime-broadcasting', 'agora-external-streaming', 'agora-rooms', 'agora-webinar', 'agora-panel-disscussions'])->nullable();
                    $table->string('plateform', 100)->nullable();
                    $table->string('size', 30)->nullable();
                    $table->text('url')->nullable();
                    $table->string('filename', 60)->nullable();
                    $table->integer('agenda_id')->nullable();
                    $table->tinyInteger('status')->default(0);
                    $table->tinyInteger('is_live')->default(0);
                    $table->string('thumbnail', 100)->nullable();
                    $table->tinyInteger('is_iframe')->nullable()->default(0);
                    $table->tinyInteger('is_meeting')->default(0);
                    $table->bigInteger('moderator')->nullable();
                    $table->bigInteger('broadcaster')->nullable()->default(0);
                    $table->text('iframe_data')->nullable();
                    $table->tinyInteger('broadcasting')->nullable()->default(0);
                    $table->enum('broadcasting_type', ['eventbuizz', 'vimeo-youtube'])->nullable();
                    $table->string('streaming_url')->nullable();
                    $table->string('streaming_key')->nullable();
                    $table->tinyInteger('private')->nullable()->default(1);
                    $table->integer('sort')->default(0);
                    $table->integer('sort_order')->nullable()->default(0);
                    $table->string('archiveId', 150)->nullable();
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
