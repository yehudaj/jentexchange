<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Entertainer;
use Illuminate\Support\Facades\Storage;

class CheckEntertainerImages extends Command
{
    protected $signature = 'check:entertainer-images';
    protected $description = 'Check entertainer records for missing profile or background images';

    public function handle()
    {
        $this->info('Scanning entertainers for missing images...');
        $missing = 0;
        Entertainer::chunk(100, function($rows) use (&$missing) {
            foreach($rows as $e){
                $pid = $e->id;
                if($e->profile_image_path && !Storage::disk('public')->exists($e->profile_image_path)){
                    $this->line("Missing profile image for entertainer {$pid}: {$e->profile_image_path}");
                    $missing++;
                }
                if($e->background_image_path && !Storage::disk('public')->exists($e->background_image_path)){
                    $this->line("Missing background image for entertainer {$pid}: {$e->background_image_path}");
                    $missing++;
                }
            }
        });
        $this->info("Scan complete. Missing files found: {$missing}");
        return 0;
    }
}
