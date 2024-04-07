<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEmailMarketingTemplateTable extends Migration
    {
        const TABLE = 'conf_email_marketing_template';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('organizer_id')->index('organizer_id');
                $table->string('name', 500);
                $table->string('list_type')->index('list_type');
                $table->integer('folder_id')->index('folder_id');
                $table->string('image', 500)->nullable();
                $table->longText('template')->nullable();
                $table->tinyInteger('template_type')->default(1);
                $table->longText('content')->nullable();
                $table->integer('created_by');
                $table->integer('updated_by');
                $table->timestamps();
                $table->softDeletes();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->integer('organizer_id')->index('organizer_id');
                    $table->string('name', 500);
                    $table->string('list_type')->index('list_type');
                    $table->integer('folder_id')->index('folder_id');
                    $table->string('image', 500)->nullable();
                    $table->longText('template')->nullable();
                    $table->tinyInteger('template_type')->default(1);
                    $table->longText('content')->nullable();
                    $table->integer('created_by');
                    $table->integer('updated_by');
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
