<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Eventbuizz\Database\EBSchema;

class CreateConfGoogleAnalyticsGmailAccounts extends Migration
{
    const TABLE = 'conf_google_analytics_gmail_accounts';
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::TABLE, function (Blueprint $table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->bigInteger('ga_accounts_count')->nullable()->default(null);
            $table->longText('refresh_token')->nullable()->default(null);
            $table->string('password')->nullable()->default(null);
            $table->enum('status', ['active', 'inqueue', 'inactive', 'completed'])->default('inqueue');
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {
            Schema::connection(config('database.archive_connection'))->create(self::TABLE, function (Blueprint $table) {
                $table->increments('id');
                $table->string('email')->unique();
                $table->bigInteger('ga_accounts_count')->nullable()->default(null);
                $table->longText('refresh_token')->nullable()->default(null);
                $table->string('password')->nullable()->default(null);
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
