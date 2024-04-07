<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfLeadHubTemplateTable extends Migration
    {
        const TABLE = 'conf_lead_hub_template';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->bigInteger('event_id')->index('event_id');
                $table->bigInteger('template_id')->index('template_id');
                $table->bigInteger('type_id')->index('type_id');
                $table->string('type', 100)->index('type');
                $table->string('title', 100);
                $table->string('subject', 100);
                $table->text('template');
                $table->tinyInteger('template_type')->default(1)->index('template_type');
                $table->longText('content')->nullable();
                $table->tinyInteger('status')->index('status');
                $table->integer('updated_by')->nullable()->index('updated_by');
                $table->timestamps();
            $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->bigInteger('event_id')->index('event_id');
                    $table->bigInteger('template_id')->index('template_id');
                    $table->bigInteger('type_id')->index('type_id');
                    $table->string('type', 100)->index('type');
                    $table->string('title', 100);
                    $table->string('subject', 100);
                    $table->text('template');
                    $table->tinyInteger('template_type')->default(1)->index('template_type');
                    $table->longText('content')->nullable();
                    $table->tinyInteger('status')->index('status');
                    $table->integer('updated_by')->nullable()->index('updated_by');
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
