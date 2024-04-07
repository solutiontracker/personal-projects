<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfAddOnsTable extends Migration
    {
        const TABLE = 'conf_add_ons';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->bigInteger('admin_id')->index('admin_id');
                $table->string('name');
                $table->string('alias');
                $table->string('description');
                $table->enum('basic_addons', ['0', '1'])->default('0')->index('basic_addons')->comment('1=basic, 0=none');
                $table->integer('module_id')->index('module_id');
                $table->enum('status', ['y', 'n'])->default('y')->index('status');
                $table->timestamps();
                $table->softDeletes();
            });

            if (app()->environment('live')) {

                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->bigInteger('admin_id');
                    $table->string('name');
                    $table->string('alias');
                    $table->string('description');
                    $table->enum('basic_addons', ['0', '1'])->default('0')->comment('1=basic, 0=none');
                    $table->integer('module_id');
                    $table->enum('status', ['y', 'n'])->default('y');
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
