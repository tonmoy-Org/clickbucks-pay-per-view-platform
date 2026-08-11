<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

DB::table('notification_templates')->where('act', 'SVER_CODE')->update([
    'sms_body' => "Hi @{{username}} ({{fullname}}),\n\nPPVBucks Verification\n\nYour code is {{code}}. Enter it to verify your phone. Do not share it."
]);

echo "Done PPVBucks\n";
