<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Entertainer;

$e = Entertainer::find(1);
if(!$e) { echo "no entertainer 1\n"; exit(0); }
$e->types = ['Girls','Mixed'];
$e->audiences = ['Boys'];
$e->cities = ['Brooklyn','Queens','NYC','Lakewood NJ','Monsey'];
$e->pricing_packages = [['price'=>1500,'description'=>'juggling - 1 hr']];
$e->save();

echo "saved\n";
