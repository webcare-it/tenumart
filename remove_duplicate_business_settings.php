<?php

// Script to remove duplicate entries in business_settings table, keeping only one record for each type

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\BusinessSetting;

echo "Starting duplicate removal process for business settings...\n";

// First, let's identify duplicates
$duplicates = DB::table('business_settings')
    ->select('type', DB::raw('COUNT(*) as count'))
    ->groupBy('type')
    ->having('count', '>', 1)
    ->get();

echo "Found " . $duplicates->count() . " types with duplicates.\n";

$totalRemoved = 0;

foreach ($duplicates as $duplicate) {
    echo "Processing type: {$duplicate->type} ({$duplicate->count} entries)\n";
    
    // Get all IDs for this type
    $entries = DB::table('business_settings')
        ->where('type', $duplicate->type)
        ->orderBy('id')
        ->get(['id']);
    
    // Keep the first entry, delete the rest
    $ids = $entries->pluck('id')->toArray();
    $keepId = array_shift($ids); // Keep the first one
    $deleteIds = $ids; // Delete the rest
    
    if (!empty($deleteIds)) {
        $deleted = DB::table('business_settings')
            ->whereIn('id', $deleteIds)
            ->delete();
        
        echo "  Kept ID: {$keepId}, Deleted " . count($deleteIds) . " duplicate(s)\n";
        $totalRemoved += count($deleteIds);
    }
}

echo "\nDuplicate removal process completed.\n";
echo "Total duplicates removed: {$totalRemoved}\n";

// Verification: Check if there are still duplicates
$remainingDuplicates = DB::table('business_settings')
    ->select('type', DB::raw('COUNT(*) as count'))
    ->groupBy('type')
    ->having('count', '>', 1)
    ->get();

if ($remainingDuplicates->count() > 0) {
    echo "\nWARNING: Still found " . $remainingDuplicates->count() . " types with duplicates:\n";
    foreach ($remainingDuplicates as $dup) {
        echo "  - {$dup->type}: {$dup->count} entries\n";
    }
} else {
    echo "\nSUCCESS: No duplicates remaining!\n";
}

echo "Script finished.\n";