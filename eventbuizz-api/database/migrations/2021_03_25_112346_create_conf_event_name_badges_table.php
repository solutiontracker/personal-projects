<?php

    use App\Eventbuizz\Database\EBSchema;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class CreateConfEventNameBadgesTable extends Migration
    {
        const TABLE = 'conf_event_name_badges';

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create(self::TABLE, function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('name');
                $table->bigInteger('organizer_id')->index('organizer_id');
                $table->integer('parent_id');
                $table->longText('content');
                $table->longText('body');
                $table->longText('body_2');
                $table->string('height', 50);
                $table->string('height_2', 50);
                $table->string('width', 50);
                $table->string('width_2', 50);
                $table->string('column', 50)->default('1');
                $table->string('column_spacing', 50)->default('0');
                $table->string('row_spacing', 50)->default('0');
                $table->string('top', 50)->default('0');
                $table->string('left', 50)->default('0');
                $table->string('right', 50)->default('0');
                $table->string('bottom', 50)->default('0');
                $table->tinyInteger('mirror')->default(0);
                $table->tinyInteger('crop_marks')->default(0);
                $table->tinyInteger('hide_border')->default(0);
                $table->integer('count')->default(0);
                $table->tinyInteger('table_badge')->default(0);
                $table->tinyInteger('IsEmail')->default(1);
                $table->tinyInteger('IsOrganization')->default(1);
                $table->tinyInteger('IsJobTitle')->default(1);
                $table->tinyInteger('IsCompanyName')->default(1);
                $table->tinyInteger('IsDept')->default(1);
                $table->tinyInteger('IsDelegate')->default(1);
                $table->tinyInteger('IsTable')->default(1);
                $table->tinyInteger('IsEventName')->default(1);
                $table->tinyInteger('IsInitial')->default(1);
                $table->tinyInteger('IsFirstName')->default(1);
                $table->tinyInteger('IsLastName')->default(1);
                $table->tinyInteger('IsIndustry')->default(1);
                $table->tinyInteger('IsDropDown')->default(1);
                $table->tinyInteger('IsCountry')->default(1);
                $table->tinyInteger('IsJobTasks')->default(1);
                $table->tinyInteger('IsInterests')->default(1);
                $table->tinyInteger('IsNetworkGroup')->default(1);
                $table->tinyInteger('IsWebsite')->default(1);
                $table->timestamps();
            });


            if (app()->environment('live')) {
                Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                    $table->integer('id');
                    $table->string('name');
                    $table->bigInteger('organizer_id')->index('organizer_id');
                    $table->integer('parent_id');
                    $table->longText('content');
                    $table->longText('body');
                    $table->longText('body_2');
                    $table->string('height', 50);
                    $table->string('height_2', 50);
                    $table->string('width', 50);
                    $table->string('width_2', 50);
                    $table->string('column', 50)->default('1');
                    $table->string('column_spacing', 50)->default('0');
                    $table->string('row_spacing', 50)->default('0');
                    $table->string('top', 50)->default('0');
                    $table->string('left', 50)->default('0');
                    $table->string('right', 50)->default('0');
                    $table->string('bottom', 50)->default('0');
                    $table->tinyInteger('mirror')->default(0);
                    $table->tinyInteger('crop_marks')->default(0);
                    $table->tinyInteger('hide_border')->default(0);
                    $table->integer('count')->default(0);
                    $table->tinyInteger('table_badge')->default(0);
                    $table->tinyInteger('IsEmail')->default(1);
                    $table->tinyInteger('IsOrganization')->default(1);
                    $table->tinyInteger('IsJobTitle')->default(1);
                    $table->tinyInteger('IsCompanyName')->default(1);
                    $table->tinyInteger('IsDept')->default(1);
                    $table->tinyInteger('IsDelegate')->default(1);
                    $table->tinyInteger('IsTable')->default(1);
                    $table->tinyInteger('IsEventName')->default(1);
                    $table->tinyInteger('IsInitial')->default(1);
                    $table->tinyInteger('IsFirstName')->default(1);
                    $table->tinyInteger('IsLastName')->default(1);
                    $table->tinyInteger('IsIndustry')->default(1);
                    $table->tinyInteger('IsDropDown')->default(1);
                    $table->tinyInteger('IsCountry')->default(1);
                    $table->tinyInteger('IsJobTasks')->default(1);
                    $table->tinyInteger('IsInterests')->default(1);
                    $table->tinyInteger('IsNetworkGroup')->default(1);
                    $table->tinyInteger('IsWebsite')->default(1);
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
