<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique()->constrained('products')->cascadeOnDelete();
            $table->foreignId('winner_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('status', [
                'scheduled',   // الأدمن حدد الميعاد وبس
                'active',      // المزاد فاتح دلوقتي
                'ended',       // المزاد خلص
                'cancelled',   // اتلغى
            ])->default('scheduled');

            $table->decimal('starting_price', 10, 2);   // السعر الابتدائي
            $table->decimal('current_price', 10, 2);     // أعلى bid دلوقتي
            $table->decimal('insurance_rate', 5, 2);     // نسبة التأمين % (مثلاً 10.00 = 10%)

            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};
