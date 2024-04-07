<?php

use App\Eventbuizz\Database\EBSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHubAdminMessagesTables extends Migration
{
    const TABLE_DRAFT = 'conf_hub_admin_draft_messages';
    const TABLE_MESSAGES = 'conf_hub_admin_messages';
    const TABLE_MESSAGES_RECIPIENT = 'conf_hub_admin_message_recipient';
    const TABLE_MESSAGES_THREAD = 'conf_hub_admin_message_thread';
    const TABLE_MESSAGES_THREAD_PARTICIPANT = 'conf_hub_admin_message_thread_participant';

    public function up()
    {
        // TABLE_DRAFT
        Schema::create(self::TABLE_DRAFT, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('event_id')->index('event_id')->nullable();
            $table->integer('from_id')->index('from_id')->nullable();
            $table->integer('thread_id')->index('thread_id')->nullable();
            $table->integer('type_id')->index('type_id')->nullable();
            $table->string('recipient_ids')->nullable();
            $table->string('subject')->nullable();
            $table->string('body')->nullable();
            $table->enum('user_type', ['sub_admin', 'hub_admin'])->nullable();
            $table->enum('type', ['sponsor', 'exhibitor'])->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // TABLE_MESSAGES
        Schema::create(self::TABLE_MESSAGES, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('event_id')->index('event_id')->nullable();
            $table->integer('from_id')->index('from_id')->nullable();
            $table->integer('thread_id')->index('thread_id')->nullable();
            $table->integer('type_id')->index('type_id')->nullable();
            $table->tinyInteger('initiate')->nullable()->default(0);
            $table->string('subject')->nullable();
            $table->longText('body')->nullable();
            $table->enum('user_type', ['sub_admin', 'hub_admin'])->nullable();
            $table->enum('type', ['sponsor', 'exhibitor'])->nullable();
            $table->dateTime('date_sent')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // TABLE_MESSAGES_RECIPIENT
        Schema::create(self::TABLE_MESSAGES_RECIPIENT, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('message_id')->index('message_id')->nullable();
            $table->integer('recipient_id')->index('recipient_id')->nullable();
            $table->integer('thread_id')->index('thread_id')->nullable();
            $table->enum('user_type', ['sub_admin', 'hub_admin'])->nullable();
            $table->tinyInteger('is_read')->nullable()->default(0);
            $table->tinyInteger('is_reminder')->nullable()->default(0);
            $table->tinyInteger('is_star')->nullable()->default(0);
            $table->tinyInteger('is_deleted')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // TABLE_MESSAGES_THREAD
        Schema::create(self::TABLE_MESSAGES_THREAD, function (Blueprint $table) {
            $table->integer('id', true);
            $table->timestamps();
            $table->softDeletes();
        });

        // TABLE_MESSAGES_THREAD_PARTICIPANT
        Schema::create(self::TABLE_MESSAGES_THREAD_PARTICIPANT, function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('participant_id')->index('participant_id')->nullable();
            $table->integer('thread_id')->index('thread_id')->nullable();
            $table->enum('user_type', ['sub_admin', 'hub_admin'])->nullable();
            $table->tinyInteger('is_reminder')->nullable()->default(0);
            $table->tinyInteger('is_star')->nullable()->default(0);
            $table->enum('participant_type', ['sub_admin', 'hub_admin'])->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        if (app()->environment('live')) {

            // TABLE_DRAFT
            Schema::connection(config('database.archive_connection'))->create(self::TABLE_DRAFT, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('event_id')->index('event_id')->nullable();
                $table->integer('from_id')->index('from_id')->nullable();
                $table->integer('thread_id')->index('thread_id')->nullable();
                $table->integer('type_id')->index('type_id')->nullable();
                $table->string('recipient_ids')->nullable();
                $table->string('subject')->nullable();
                $table->string('body')->nullable();
                $table->enum('user_type', ['sub_admin', 'hub_admin'])->nullable();
                $table->enum('type', ['sponsor', 'exhibitor'])->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            // TABLE_MESSAGES
            Schema::connection(config('database.archive_connection'))->create(self::TABLE_MESSAGES, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('event_id')->index('event_id')->nullable();
                $table->integer('from_id')->index('from_id')->nullable();
                $table->integer('thread_id')->index('thread_id')->nullable();
                $table->integer('type_id')->index('type_id')->nullable();
                $table->tinyInteger('initiate')->nullable()->default(0);
                $table->string('subject')->nullable();
                $table->longText('body')->nullable();
                $table->enum('user_type', ['sub_admin', 'hub_admin'])->nullable();
                $table->enum('type', ['sponsor', 'exhibitor'])->nullable();
                $table->dateTime('date_sent')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            // TABLE_MESSAGES_RECIPIENT
            Schema::connection(config('database.archive_connection'))->create(self::TABLE_MESSAGES_RECIPIENT, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('message_id')->index('message_id')->nullable();
                $table->integer('recipient_id')->index('recipient_id')->nullable();
                $table->integer('thread_id')->index('thread_id')->nullable();
                $table->enum('user_type', ['sub_admin', 'hub_admin'])->nullable();
                $table->tinyInteger('is_read')->nullable()->default(0);
                $table->tinyInteger('is_reminder')->nullable()->default(0);
                $table->tinyInteger('is_star')->nullable()->default(0);
                $table->tinyInteger('is_deleted')->nullable()->default(0);
                $table->timestamps();
                $table->softDeletes();
            });

            // TABLE_MESSAGES_THREAD
            Schema::connection(config('database.archive_connection'))->create(self::TABLE_MESSAGES_THREAD, function (Blueprint $table) {
                $table->integer('id');
                $table->timestamps();
                $table->softDeletes();
            });

            // TABLE_MESSAGES_THREAD_PARTICIPANT
            Schema::connection(config('database.archive_connection'))->create(self::TABLE_MESSAGES_THREAD_PARTICIPANT, function (Blueprint $table) {
                $table->integer('id');
                $table->integer('participant_id')->index('participant_id')->nullable();
                $table->integer('thread_id')->index('thread_id')->nullable();
                $table->enum('user_type', ['sub_admin', 'hub_admin'])->nullable();
                $table->tinyInteger('is_reminder')->nullable()->default(0);
                $table->tinyInteger('is_star')->nullable()->default(0);
                $table->enum('participant_type', ['sub_admin', 'hub_admin'])->nullable();
                $table->timestamps();
                $table->softDeletes();
            });

            EBSchema::createBeforeDeleteTrigger(self::TABLE_DRAFT);
            EBSchema::createBeforeDeleteTrigger(self::TABLE_MESSAGES);
            EBSchema::createBeforeDeleteTrigger(self::TABLE_MESSAGES_RECIPIENT);
            EBSchema::createBeforeDeleteTrigger(self::TABLE_MESSAGES_THREAD);
            EBSchema::createBeforeDeleteTrigger(self::TABLE_MESSAGES_THREAD_PARTICIPANT);
        }
    }

    public function down()
    {
        // TABLE_DRAFT
        EBSchema::dropDeleteTrigger(self::TABLE_DRAFT);
        Schema::dropIfExists(self::TABLE_DRAFT);
        Schema::connection(config('database.archive_connection'))->dropIfExists(self::TABLE_DRAFT);

        // TABLE_MESSAGES
        EBSchema::dropDeleteTrigger(self::TABLE_MESSAGES);
        Schema::dropIfExists(self::TABLE_MESSAGES);
        Schema::connection(config('database.archive_connection'))->dropIfExists(self::TABLE_MESSAGES);

        // TABLE_MESSAGES_RECIPIENT
        EBSchema::dropDeleteTrigger(self::TABLE_MESSAGES_RECIPIENT);
        Schema::dropIfExists(self::TABLE_MESSAGES_RECIPIENT);
        Schema::connection(config('database.archive_connection'))->dropIfExists(self::TABLE_MESSAGES_RECIPIENT);

        // TABLE_MESSAGES_THREAD
        EBSchema::dropDeleteTrigger(self::TABLE_MESSAGES_THREAD);
        Schema::dropIfExists(self::TABLE_MESSAGES_THREAD);
        Schema::connection(config('database.archive_connection'))->dropIfExists(self::TABLE_MESSAGES_THREAD);

        // TABLE_MESSAGES_THREAD_PARTICIPANT
        EBSchema::dropDeleteTrigger(self::TABLE_MESSAGES_THREAD_PARTICIPANT);
        Schema::dropIfExists(self::TABLE_MESSAGES_THREAD_PARTICIPANT);
        Schema::connection(config('database.archive_connection'))->dropIfExists(self::TABLE_MESSAGES_THREAD_PARTICIPANT);
    }
}
