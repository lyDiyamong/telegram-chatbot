<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('telegram_user_id')->constrained('telegram_users')->onDelete('cascade');
            $table->text('content')->nullable();
            $table->boolean('from_admin')->default(false);
            $table->boolean('is_read')->default(false);
            $table->string('file_path')->nullable()->after('content');
            $table->string('file_type')->nullable()->after('file_path');
            $table->string('file_name')->nullable()->after('file_type');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_messages');
    }
};
