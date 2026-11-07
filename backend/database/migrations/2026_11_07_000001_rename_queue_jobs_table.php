<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Bảng `jobs` trong thiết kế là tin tuyển dụng, trùng tên với bảng queue mặc định
 * của Laravel. Đổi bảng queue thành `queue_jobs` (xem config/queue.php).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('jobs', 'queue_jobs');
    }

    public function down(): void
    {
        Schema::rename('queue_jobs', 'jobs');
    }
};
