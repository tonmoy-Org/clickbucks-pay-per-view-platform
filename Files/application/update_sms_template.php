<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Update global SMS template
DB::table('general_settings')->update([
    'sms_body' => "{{message}}"
]);

// Update SVER_CODE
DB::table('notification_templates')->where('act', 'SVER_CODE')->update([
    'sms_body' => "PPV Bucks \nVerification Code: {{code}}"
]);

echo "Done PPVBucks\n";
