<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_chat_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('chats', function (Blueprint $t) {
      $t->id();
      $t->foreignId('user_id')->constrained()->cascadeOnDelete();
      $t->string('title')->nullable();
      $t->timestamps();
    });

    Schema::create('chat_messages', function (Blueprint $t) {
      $t->id();
      $t->foreignId('chat_id')->constrained()->cascadeOnDelete();
      $t->enum('role', ['user','assistant','system']);
      $t->longText('content');
      $t->json('meta')->nullable();
      $t->timestamps();
      $t->index(['chat_id','created_at']);
    });
  }
  public function down(): void {
    Schema::dropIfExists('chat_messages');
    Schema::dropIfExists('chats');
  }
};
