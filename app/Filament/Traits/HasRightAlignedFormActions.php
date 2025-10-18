<?php

namespace App\Filament\Traits;

use Filament\Support\Enums\Alignment;

/**
 * Trait HasRightAlignedFormActions
 *
 * Aligns form action buttons to the right (South African UX standard).
 * Apply this trait to any Filament resource page (Create, Edit, View) to
 * automatically align form buttons to the right with proper spacing.
 *
 * Features:
 * - Right-aligned buttons (SA UX standard)
 * - Generous spacing between buttons (24px gap)
 * - Visual separator with top border
 * - Proper vertical spacing (40px top margin, 24px top padding)
 */
trait HasRightAlignedFormActions
{
    /**
     * Align form actions to the right (South African UX standard)
     */
    public function getFormActionsAlignment(): Alignment | string
    {
        return Alignment::End;
    }

    /**
     * Get CSS classes for form actions wrapper
     * Provides consistent spacing across all forms
     */
    protected function getFormActionsClasses(): string
    {
        return 'flex items-center justify-end gap-6 mt-10 pt-6 border-t border-commatix-200 dark:border-commatix-700';
    }

    /**
     * Get CSS classes for form action buttons
     * Returns array with classes for secondary and primary buttons
     */
    protected function getFormActionButtonClasses(): array
    {
        return [
            'secondary' => 'hover:bg-commatix-50 dark:hover:bg-commatix-900 transition-colors min-w-[120px]',
            'primary' => 'shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105 active:scale-95 min-w-[120px]',
        ];
    }
}
