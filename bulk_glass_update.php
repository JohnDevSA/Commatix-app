#!/usr/bin/env php
<?php

/**
 * Bulk Glass Design Update Script
 *
 * This script adds glassmorphism design to all remaining Filament resources.
 * It adds the HasGlassmorphicForms trait and updates form styling.
 */

$resourcesDir = __DIR__ . '/app/Filament/Resources';

// Resources to update
$resourcesToUpdate = [
    'DivisionResource.php',
    'DocumentTypeResource.php',
    'IndustryResource.php',
    'SubscriberResource.php',
    'SubscriberListResource.php',
    'TenantWorkflowTemplateResource.php',
    'TenantUsageResource.php',
    'TenantTopUpResource.php',
    'TenantSubscriptionResource.php',
    'UserTypeResource.php',
];

$traitImport = "use App\Filament\Traits\HasGlassmorphicForms;";
$traitUsage = "    use HasGlassmorphicForms;\n";

$updatedCount = 0;
$errors = [];

foreach ($resourcesToUpdate as $resourceFile) {
    $filePath = $resourcesDir . '/' . $resourceFile;

    if (!file_exists($filePath)) {
        $errors[] = "File not found: $resourceFile";
        continue;
    }

    $content = file_get_contents($filePath);

    // Skip if already has the trait
    if (strpos($content, 'HasGlassmorphicForms') !== false) {
        echo "✓ $resourceFile already has HasGlassmorphicForms trait\n";
        continue;
    }

    // Add trait import after other use statements
    $pattern = '/(use Filament\\\\Resources\\\\Resource;)/';
    $replacement = "$1\n$traitImport";
    $content = preg_replace($pattern, $replacement, $content);

    // Add trait usage after class declaration
    $pattern = '/(class \w+Resource extends Resource\n\{)/';
    $replacement = "$1\n$traitUsage";
    $content = preg_replace($pattern, $replacement, $content);

    // Update Section components to use glass styling
    $content = preg_replace(
        '/Components\\\\Section::make\((.*?)\)\s*->schema\(/s',
        'Components\\Section::make($1)' . "\n                    ->icon('heroicon-m-information-circle')\n                    ->extraAttributes(self::glassCard())\n                    ->schema(",
        $content
    );

    // Update TextInput to use glass-input
    $content = preg_replace(
        '/(FormComponents\\\\TextInput::make\(.*?\))(\s*->(?!extraInputAttributes))/s',
        "$1\n                                    ->extraInputAttributes(self::glassInput())$2",
        $content,
        1 // Only first occurrence per input
    );

    // Update Textarea to use glass-input
    $content = preg_replace(
        '/(FormComponents\\\\Textarea::make\(.*?\))(\s*->(?!extraInputAttributes))/s',
        "$1\n                            ->extraInputAttributes(self::glassInput())$2",
        $content,
        1
    );

    // Update Select to use glass-input
    $content = preg_replace(
        '/(FormComponents\\\\Select::make\(.*?\))(\s*->(?!extraAttributes))/s',
        "$1\n                                    ->extraAttributes(self::glassInput())$2",
        $content,
        1
    );

    // Write updated content
    if (file_put_contents($filePath, $content)) {
        echo "✓ Updated $resourceFile\n";
        $updatedCount++;
    } else {
        $errors[] = "Failed to write: $resourceFile";
    }
}

echo "\n";
echo "========================================\n";
echo "Bulk Glass Update Summary\n";
echo "========================================\n";
echo "Updated: $updatedCount resources\n";

if (!empty($errors)) {
    echo "\nErrors:\n";
    foreach ($errors as $error) {
        echo "  ✗ $error\n";
    }
}

echo "\n✨ Done!\n";
