<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE leads MODIFY approval_status TINYINT NOT NULL DEFAULT 0 COMMENT '0=pending, 1=approved, 2=rejected, 3=unavailable, 4=completed'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE leads MODIFY approval_status TINYINT NOT NULL DEFAULT 0 COMMENT ''");
    }
};