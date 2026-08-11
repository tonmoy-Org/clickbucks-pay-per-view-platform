<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$gs = \App\Models\GeneralSetting::first();
$gs->sn = 1;
$gs->save();
echo "Done";
