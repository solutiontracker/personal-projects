<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfGoogleAnalyticsAccounts extends Migration
{
    const TABLE = 'conf_google_analytics_accounts';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('ga_gmail_id');
            $table->string('ga_account_name');
            $table->string('ga_account_id')->unique();
            $table->bigInteger('ga_properties_count');
            $table->enum('status', ['active', 'inqueue', 'inactive', 'completed'])->default('inqueue');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->foreignId('ga_gmail_id');
                $table->string('ga_account_name');
                $table->string('ga_account_id')->unique();
                $table->bigInteger('ga_properties_count');
                $table->enum('status', ['active', 'inqueue', 'inactive', 'completed'])->default('inqueue');
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
