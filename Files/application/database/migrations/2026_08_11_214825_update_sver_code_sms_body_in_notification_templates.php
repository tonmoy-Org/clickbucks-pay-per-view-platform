<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $newBody = "**PPVBucks Verification**\n\nYour phone verification code is **{{code}}**.\n\nEnter this code to verify your phone. Do not share it.";
        DB::table('notification_templates')->where('act', 'SVER_CODE')->update(['sms_body' => $newBody]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $oldBody = "Your phone verification code is: {{code}}";
        DB::table('notification_templates')->where('act', 'SVER_CODE')->update(['sms_body' => $oldBody]);
    }
};
