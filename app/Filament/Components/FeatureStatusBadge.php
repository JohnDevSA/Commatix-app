<?php

namespace App\Filament\Components;

use Filament\Support\Enums\IconPosition;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FeatureStatusBadge extends Component
{
    public function __construct(
        public string $status = 'available',
        public ?string $message = null,
        public ?string $tooltip = null,
        public bool $strikethrough = false,
    ) {}

    public function render(): View
    {
        return view('filament.components.feature-status-badge');
    }

    /**
     * Get the badge color based on status
     */
    public function getColor(): string
    {
        return match ($this->status) {
            'available' => 'success',
            'beta' => 'info',
            'coming-soon' => 'warning',
            'unavailable' => 'danger',
            'deprecated' => 'gray',
            default => 'gray',
        };
    }

    /**
     * Get the icon for the status
     */
    public function getIcon(): ?string
    {
        return match ($this->status) {
            'available' => 'heroicon-m-check-circle',
            'beta' => 'heroicon-m-beaker',
            'coming-soon' => 'heroicon-m-clock',
            'unavailable' => 'heroicon-m-x-circle',
            'deprecated' => 'heroicon-m-exclamation-triangle',
            default => null,
        };
    }

    /**
     * Get default message for status
     */
    public function getMessage(): string
    {
        if ($this->message) {
            return $this->message;
        }

        return match ($this->status) {
            'available' => 'Available',
            'beta' => 'Beta',
            'coming-soon' => 'Coming Soon',
            'unavailable' => 'Unavailable',
            'deprecated' => 'Deprecated',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get CSS classes for the badge
     */
    public function getCssClasses(): string
    {
        $classes = [
            'inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium',
        ];

        // Color classes
        $classes[] = match ($this->getColor()) {
            'success' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'warning' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            'danger' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'gray' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200',
            default => 'bg-gray-100 text-gray-800',
        };

        // Strikethrough effect
        if ($this->strikethrough) {
            $classes[] = 'line-through opacity-75';
        }

        return implode(' ', $classes);
    }
}
