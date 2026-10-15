<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Capsule\Manager as CapsuleManager;

return new class extends Migration
{
    public function up(): void
    {
        CapsuleManager::schema()->create('contact_submissions', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('email', 255);
            $table->text('message');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        CapsuleManager::schema()->dropIfExists('contact_submissions');
    }
};
