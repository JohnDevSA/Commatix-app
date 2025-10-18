<?php

namespace App\Filament\Traits;

/**
 * Trait HasGlassmorphicForms
 *
 * Provides helper methods for consistently applying glassmorphism design system
 * across Filament resources. This trait implements the Commatix design system
 * as documented in DESIGN_SYSTEM.md.
 *
 * Features:
 * - Glass card styling for sections
 * - Glass input styling for form fields
 * - Consistent animations (fade-in, slide-up)
 * - Animation delay sequencing
 * - Icon integration for sections
 *
 * Usage:
 * ```php
 * use HasGlassmorphicForms;
 *
 * // In your form schema:
 * Section::make('Section Title')
 *     ->schema([...])
 *     ->extraAttributes($this->glassCard())
 *
 * TextInput::make('field')
 *     ->extraInputAttributes($this->glassInput())
 * ```
 */
trait HasGlassmorphicForms
{
    /**
     * Get glass card styling attributes with optional animation delay
     *
     * @param string $animation Animation type ('fade-in', 'slide-up', or 'none')
     * @param float $delay Animation delay in seconds (e.g., 0.1, 0.2, 0.3)
     * @return array
     */
    protected static function glassCard(string $animation = 'fade-in', float $delay = 0): array
    {
        $classes = ['glass-card'];

        if ($animation !== 'none') {
            $classes[] = "animate-{$animation}";
        }

        $attributes = ['class' => implode(' ', $classes)];

        if ($delay > 0 && $animation !== 'none') {
            $attributes['style'] = "animation-delay: {$delay}s";
        }

        return $attributes;
    }

    /**
     * Get glass input styling attributes
     *
     * @return array
     */
    protected static function glassInput(): array
    {
        return ['class' => 'glass-input'];
    }

    /**
     * Get glass card with sequential animation delays
     *
     * @param int $index Zero-based index for staggered animation (0, 1, 2, etc.)
     * @param string $animation Animation type ('fade-in' or 'slide-up')
     * @param float $delayIncrement Delay increment per index (default: 0.1s)
     * @return array
     */
    protected static function glassCardSequence(int $index, string $animation = 'fade-in', float $delayIncrement = 0.1): array
    {
        return self::glassCard($animation, $index * $delayIncrement);
    }

    /**
     * Get common section configuration with icon, description, and glass styling
     *
     * @param string $title Section title
     * @param string $description Section description
     * @param string $icon Heroicon name (e.g., 'heroicon-m-user')
     * @param int $animationIndex Animation sequence index
     * @return array
     */
    protected static function glassSectionConfig(
        string $title,
        string $description,
        string $icon,
        int $animationIndex = 0
    ): array {
        return [
            'heading' => $title,
            'description' => $description,
            'icon' => $icon,
            'extraAttributes' => self::glassCardSequence($animationIndex),
        ];
    }
}
