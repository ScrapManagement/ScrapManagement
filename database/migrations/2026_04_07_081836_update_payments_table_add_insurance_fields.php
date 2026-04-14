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
        Schema::table('payments', function (Blueprint $table) {
            $table->enum('type', ['package', 'insurance'])
                ->default('package')
                ->after('gateway');

            $table->foreignId('auction_participant_id')
                ->nullable()
                ->after('type')
                ->constrained('auction_participants')
                ->nullOnDelete();

            $table->foreignId('package_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            //
        });
    }
};
