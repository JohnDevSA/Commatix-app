# Commatix UI Patterns

This document contains reusable UI patterns for consistent implementation across the Commatix application.

## Form Action Buttons (South African UX Standard)

All forms should have right-aligned action buttons with proper spacing.

### Standard Implementation

For custom Blade views (like custom pages):

```blade
{{-- Form action buttons wrapper --}}
<div class="flex items-center justify-end gap-6 mt-10 pt-6 border-t border-commatix-200 dark:border-commatix-700">
    {{-- Secondary/Cancel button (optional) --}}
    <x-filament::button
        type="button"
        color="gray"
        outlined
        wire:click="cancel"
        icon="heroicon-o-x-mark"
        class="hover:bg-commatix-50 dark:hover:bg-commatix-900 transition-colors min-w-[140px]"
    >
        Cancel
    </x-filament::button>

    {{-- Primary action button --}}
    <x-filament::button
        type="submit"
        color="primary"
        icon="heroicon-o-check"
        class="shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105 active:scale-95 min-w-[140px]"
    >
        Save Changes
    </x-filament::button>
</div>
```

### For Filament Resource Pages

For Create, Edit, and View pages, use the `HasRightAlignedFormActions` trait:

```php
<?php

namespace App\Filament\Resources\YourResource\Pages;

use App\Filament\Resources\YourResource;
use App\Filament\Traits\HasRightAlignedFormActions;
use Filament\Resources\Pages\EditRecord;

class EditYourResource extends EditRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = YourResource::class;

    // The trait automatically right-aligns buttons
    // No additional configuration needed!
}
```

### Spacing Standards

- **Button gap**: `gap-6` (24px) - Generous spacing between buttons
- **Top margin**: `mt-10` (40px) - Clear separation from form content
- **Top padding**: `pt-6` (24px) - Space after border
- **Button min-width**: `min-w-[140px]` - Prevents narrow buttons, ensures consistent sizing

### Design Rationale

1. **Right alignment** - South African UX standard for form actions
2. **Button order** - Secondary (left) → Primary (right) follows user reading flow
3. **Generous spacing** - 24px gap prevents accidental clicks
4. **Visual separator** - Top border clearly separates actions from content
5. **Vertical breathing room** - 40px top margin ensures actions don't feel cramped
6. **Minimum width** - Prevents buttons from being too narrow, improves touch targets
7. **Hover effects** - Scale and shadow on primary buttons provides visual feedback

### Button Types and Icons

#### Common Primary Actions
- **Save**: `heroicon-o-check` or `heroicon-o-check-circle`
- **Create**: `heroicon-o-plus-circle`
- **Submit**: `heroicon-o-paper-airplane`
- **Confirm**: `heroicon-o-check-badge`

#### Common Secondary Actions
- **Cancel**: `heroicon-o-x-mark`
- **Reset**: `heroicon-o-arrow-path`
- **Back**: `heroicon-o-arrow-left`
- **Delete**: `heroicon-o-trash` (use red color)

### Accessibility

- **Touch targets**: Minimum 140px width ensures 44px+ height with padding
- **Focus rings**: Filament buttons include focus rings by default
- **Color contrast**: Gray outlined and primary colored buttons meet WCAG AA
- **Keyboard navigation**: Tab order flows naturally from left to right

### Mobile Responsive

On mobile screens, buttons can stack vertically:

```blade
<div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-4 sm:gap-6 mt-10 pt-6 border-t border-commatix-200 dark:border-commatix-700">
    {{-- Buttons stack on mobile, side-by-side on tablet+ --}}
</div>
```

## Examples Across the App

### Custom Page Example (OrganizationSettings)

```blade
<form wire:submit="save" class="space-y-6">
    {{ $this->form }}

    <div class="flex items-center justify-end gap-6 mt-10 pt-6 border-t border-commatix-200 dark:border-commatix-700">
        <x-filament::button
            type="button"
            color="gray"
            outlined
            wire:click="mount"
            icon="heroicon-o-arrow-path"
            class="hover:bg-commatix-50 dark:hover:bg-commatix-900 transition-colors min-w-[140px]"
        >
            Reset Changes
        </x-filament::button>

        <x-filament::button
            type="submit"
            color="primary"
            icon="heroicon-o-check"
            class="shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-105 active:scale-95 min-w-[140px]"
        >
            Save Changes
        </x-filament::button>
    </div>
</form>
```

### Resource Page Example

```php
// app/Filament/Resources/TaskResource/Pages/EditTask.php
class EditTask extends EditRecord
{
    use HasRightAlignedFormActions;

    protected static string $resource = TaskResource::class;

    // Buttons are automatically right-aligned with proper spacing!
}
```

## Loading States

### Loading Overlay Component

For form submissions and async operations, use the `<x-loading-overlay>` component:

```blade
<form wire:submit="save">
    {{-- Form fields --}}

    {{-- Loading overlay --}}
    <x-loading-overlay
        target="save"
        message="Saving changes..."
        description="Please wait"
    />

    {{-- Form actions --}}
</form>
```

### Inline Button Loading States

For individual buttons, show loading state inline:

```blade
<x-filament::button
    type="submit"
    wire:loading.attr="disabled"
    wire:target="save"
>
    <span wire:loading.remove wire:target="save">Save Changes</span>
    <span wire:loading wire:target="save" class="flex items-center gap-2">
        <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Saving...
    </span>
</x-filament::button>
```

## South African Date Formats

### Using the HasSouthAfricanDateFormats Trait

All resources should use standardized SA date formats (DD/MM/YYYY):

```php
use App\Filament\Traits\HasSouthAfricanDateFormats;

class YourResource extends Resource
{
    use HasSouthAfricanDateFormats;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Date only: 17/10/2025
                Tables\Columns\TextColumn::make('due_date')
                    ->date(self::saDateFormat()),

                // Date with time: 17/10/2025 14:30
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(self::saDateTimeFormat()),

                // Long format: 17 October 2025
                Tables\Columns\TextColumn::make('published_at')
                    ->date(self::saLongDateFormat()),
            ]);
    }
}
```

### Available Date Format Methods

- `saDateFormat()` - DD/MM/YYYY (e.g., 17/10/2025)
- `saDateTimeFormat()` - DD/MM/YYYY HH:MM (e.g., 17/10/2025 14:30)
- `saDateTimeFullFormat()` - DD/MM/YYYY HH:MM:SS
- `saShortDateFormat()` - DD/MM/YY (e.g., 17/10/25)
- `saLongDateFormat()` - DD Month YYYY (e.g., 17 October 2025)
- `saMediumDateFormat()` - DD Mon YYYY (e.g., 17 Oct 2025)

---

**Last Updated**: 2025-10-17 (v1.1)
**Author**: Commatix UI/UX Team
