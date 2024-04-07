<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfRegistrationFormTemplatesTable extends Migration
    {
        const TABLE = 'conf_registration_form_templates';

        /**
         * Run the migrations.
         *
         * @return void
         */

        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->string('alias');
                $table->string('type');
                $table->string('title');
                $table->string('subject');
                $table->text('template');
                $table->longText('content')->nullable();
                $table->tinyInteger('template_type')->default(0)->index()->nullable();
                $table->bigInteger('event_id')->default(0)->index()->nullable();
                $table->bigInteger('registration_form_id')->default(0)->index()->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->string('alias');
                    $table->string('type');
                    $table->string('title');
                    $table->string('subject');
                    $table->text('template');
                    $table->longText('content')->nullable();
                    $table->tinyInteger('template_type')->default(0)->index()->nullable();
                    $table->bigInteger('event_id')->default(0)->index()->nullable();
                    $table->bigInteger('registration_form_id')->default(0)->index()->nullable();
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
