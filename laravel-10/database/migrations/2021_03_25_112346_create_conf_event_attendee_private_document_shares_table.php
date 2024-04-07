<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventAttendeePrivateDocumentSharesTable extends Migration
    {
        const TABLE = 'conf_event_attendee_private_document_shares';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->bigInteger('id', true);
                $table->integer('event_id');
                $table->integer('shared_by');
                $table->bigInteger('attendee_id');
                $table->bigInteger('private_document_id');
                $table->bigInteger('entity_id')->nullable();
                $table->string('entity_type', 100)->nullable();
                $table->timestamps();
            $table->softDeletes();
                $table->tinyInteger('enabled')->default(1)->comment('1=Yes,0=No');
            });

            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->bigInteger('id');
                    $table->integer('event_id');
                    $table->integer('shared_by');
                    $table->bigInteger('attendee_id');
                    $table->bigInteger('private_document_id');
                    $table->bigInteger('entity_id')->nullable();
                    $table->string('entity_type', 100)->nullable();
                    $table->timestamps();
            $table->softDeletes();
                    $table->tinyInteger('enabled')->default(1)->comment('1=Yes,0=No');
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
