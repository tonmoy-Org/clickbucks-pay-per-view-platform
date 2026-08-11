<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Update global SMS template
DB::table('general_settings')->update([
    'sms_body' => "Hi @{{username}} ,\n{{message}}"
]);

// Update SVER_CODE
DB::table('notification_templates')->where('act', 'SVER_CODE')->update([
    'sms_body' => "PPVBucks Verification\nYour code is {{code}}. Do not share it."
]);

echo "Done PPVBucks\n";
