<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Entertainer;

$e = Entertainer::find(1);
if (!$e) { echo "No entertainer 1\n"; exit(1); }
$e->profile_image_path = 'entertainer-uploads/693f0c7c50b70-test-upload.jpg';
$e->save();
echo "Updated entertainer 1 profile_image_path\n";
