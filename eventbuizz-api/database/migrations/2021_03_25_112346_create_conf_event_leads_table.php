<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventLeadsTable extends Migration
    {
        const TABLE = 'conf_event_leads';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->bigInteger('event_id');
                $table->string('device_id');
                $table->bigInteger('contact_person_id');
                $table->bigInteger('type_id');
                $table->string('contact_person_type');
                $table->string('email');
                $table->string('first_name');
                $table->string('last_name')->nullable();
                $table->integer('rating');
                $table->longText('raw_data');
                $table->string('image_file')->nullable();
                $table->string('initial')->nullable();
                $table->tinyInteger('permission_allowed')->nullable()->default(0);
                $table->dateTime('lead_date')->nullable();
                $table->longText('notes')->nullable();
                $table->timestamps();
            $table->softDeletes();
                $table->longText('term_text')->nullable();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->bigInteger('event_id');
                    $table->string('device_id');
                    $table->bigInteger('contact_person_id');
                    $table->bigInteger('type_id');
                    $table->string('contact_person_type');
                    $table->string('email');
                    $table->string('first_name');
                    $table->string('last_name')->nullable();
                    $table->integer('rating');
                    $table->longText('raw_data');
                    $table->string('image_file')->nullable();
                    $table->string('initial')->nullable();
                    $table->tinyInteger('permission_allowed')->nullable()->default(0);
                    $table->dateTime('lead_date')->nullable();
                    $table->longText('notes')->nullable();
                    $table->timestamps();
            $table->softDeletes();
                    $table->longText('term_text')->nullable();
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
