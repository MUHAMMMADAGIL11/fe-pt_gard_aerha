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
        if (!Schema::hasColumn('logaktivitas', 'detail')) {
            Schema::table('logaktivitas', function (Blueprint $table) {
                $table->text('detail')->nullable()->after('aktivitas');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('logaktivitas', 'detail')) {
            Schema::table('logaktivitas', function (Blueprint $table) {
                $table->dropColumn('detail');
            });
        }
    }
};

