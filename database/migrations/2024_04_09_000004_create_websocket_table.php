<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('websocket', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key')->nullable();
            $table->string('secret')->nullable();
            $table->integer('max_connections')->default(1000);
            $table->boolean('enable_client_messages')->default(true);
            $table->boolean('enabled')->default(true);
            $table->integer('max_backend_events_per_sec')->default(100);
            $table->integer('max_client_events_per_sec')->default(100);
            $table->integer('max_read_req_per_sec')->default(100);
            $table->json('webhooks')->nullable(); // JSON column para webhooks
            $table->tinyInteger('max_presence_members_per_channel')->nullable()->default(100);
            $table->tinyInteger('max_presence_member_size_in_kb')->nullable()->default(100);
            $table->tinyInteger('max_channel_name_length')->nullable()->default(100);
            $table->tinyInteger('max_event_channels_at_once')->nullable()->default(100);
            $table->tinyInteger('max_event_name_length')->nullable()->default(100);
            $table->tinyInteger('max_event_payload_in_kb')->nullable()->default(100);
            $table->tinyInteger('max_event_batch_size')->nullable()->default(100);
            $table->boolean('enable_user_authentication')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('websocket');
    }
};
