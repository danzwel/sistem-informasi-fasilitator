<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fasilitators', function (Blueprint $table) {
            $table->text('alamat_kantor')->nullable()->after('unit_kerja');
            $table->text('alamat_rumah')->nullable()->after('alamat_kantor');
            $table->dropColumn('alamat');
        });
    }

    public function down(): void
    {
        Schema::table('fasilitators', function (Blueprint $table) {
            $table->text('alamat')->nullable();
            $table->dropColumn(['alamat_kantor', 'alamat_rumah']);
        });
    }
};