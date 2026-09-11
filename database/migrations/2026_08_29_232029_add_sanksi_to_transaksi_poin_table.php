<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('transaksi_poin', 'sanksi')) {
            Schema::table('transaksi_poin', function (Blueprint $table) {
                $table->text('sanksi')->nullable()->after('keterangan');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('transaksi_poin', 'sanksi')) {
            Schema::table('transaksi_poin', function (Blueprint $table) {
                $table->dropColumn('sanksi');
            });
        }
    }
};