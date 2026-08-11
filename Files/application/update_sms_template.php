<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Update global SMS template
DB::table('general_settings')->update([
    'sms_body' => "Hi @{{username}} ({{fullname}}),\n\n{{message}}"
]);

// Update SVER_CODE to remove duplicate greeting
DB::table('notification_templates')->where('act', 'SVER_CODE')->update([
    'sms_body' => "PPVBucks Verification\n\nYour code is {{code}}. Enter it to verify your phone. Do not share it."
]);

echo "Done PPVBucks\n";
