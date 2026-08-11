<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Enable SMS notifications globally
$gs = \App\Models\GeneralSetting::first();
$gs->sn = 1;
$gs->save();

echo "✓ SMS notifications enabled (sn=1)\n";
echo "✓ VPS DB update complete\n";
