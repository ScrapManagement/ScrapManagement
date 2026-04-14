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
        Schema::table('users', function (Blueprint $table) {
            $table->string('id_card_front')->nullable()->after('address');   // صورة وجه البطاقة
            $table->string('id_card_back')->nullable()->after('id_card_front'); // صورة ظهر البطاقة
            $table->enum('id_card_status', [
                'not_submitted', // لسه مارفعش
                'pending',       // رافع وبينتظر مراجعة الأدمن
                'approved',      // الأدمن وافق
                'rejected',      // الأدمن رفض
            ])->default('not_submitted')->after('id_card_back');
            $table->timestamp('id_card_verified_at')->nullable()->after('id_card_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
             $table->dropColumn([
                'id_card_front',
                'id_card_back',
                'id_card_status',
                'id_card_verified_at',
            ]);
        });
    }
};
