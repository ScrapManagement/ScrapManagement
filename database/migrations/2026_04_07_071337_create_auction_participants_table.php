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
        Schema::create('auction_participants', function (Blueprint $table) {
            $table->id();
             $table->foreignId('auction_id')->constrained('auctions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // التأمين
            $table->decimal('insurance_amount', 10, 2);  // المبلغ اللي اتدفع فعلاً
            $table->enum('insurance_status', [
                'held',       // اتحجز (داخل المزاد)
                'refunded',   // رجع (خسر)
                'deducted',   // اتخصم من سعر المنتج (فايز)
                'forfeited',  // اتخصم عقوبة (فايز مش جاد)
            ])->default('held');

            // المعاينة
            $table->enum('inspection_type', ['online', 'offline'])->nullable();
            $table->enum('inspection_status', [
                'pending',    // لسه مش اتحدد
                'approved',   // اتعملت
                'skipped',    // اتخطت
            ])->default('pending');

            $table->unique(['auction_id', 'user_id']); // مشتري مايدخلش مزاد واحد أكتر من مرة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auction_participants');
    }
};
