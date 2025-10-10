# Feature Status Badge Component

## Overview

The `FeatureStatusBadge` component provides a reusable, standardized way to mark features with their availability status throughout the Commatix admin panel. It offers visual indicators with colors, icons, strikethrough effects, and tooltips.

## Purpose

This component was created to:
- Provide consistent visual feedback for feature availability
- Mark upcoming features (like Laravel Horizon) as "coming soon" or "unavailable"
- Create a standard pattern for indicating beta, deprecated, or unavailable features
- Improve UX by clearly communicating feature status to users

## Usage

### Basic Usage

```blade
<x-feature-status-badge status="available" />
```

### With Custom Message

```blade
<x-feature-status-badge
    status="coming-soon"
    message="Q1 2025"
/>
```

### With Tooltip

```blade
<x-feature-status-badge
    status="unavailable"
    message="Coming Q1 2025"
    tooltip="Dependency conflict with Vonage SMS client (PHP 8.4). Actively monitoring for resolution."
/>
```

### With Strikethrough Effect

```blade
<x-feature-status-badge
    status="unavailable"
    message="Coming Q1 2025"
    :strikethrough="true"
/>
```

### Full Example (Horizon Use Case)

```blade
<x-feature-status-badge
    status="unavailable"
    message="Coming Q1 2025"
    tooltip="Dependency conflict with Vonage SMS client (PHP 8.4). Actively monitoring for resolution."
    :strikethrough="true"
/>
```

## Available Statuses

| Status | Color | Icon | Default Message | Use Case |
|--------|-------|------|-----------------|----------|
| `available` | Green | Check Circle | "Available" | Feature is ready to use |
| `beta` | Blue | Beaker | "Beta" | Feature is experimental |
| `coming-soon` | Orange | Clock | "Coming Soon" | Feature is planned |
| `unavailable` | Red | X Circle | "Unavailable" | Feature cannot be used |
| `deprecated` | Gray | Warning | "Deprecated" | Feature is being phased out |

## Component Properties

### `status` (string, required)
The availability status of the feature. Accepts: `available`, `beta`, `coming-soon`, `unavailable`, `deprecated`.

**Default:** `'available'`

### `message` (string, optional)
Custom message to display instead of the default status message.

**Default:** `null` (uses default message based on status)

**Example:**
```blade
<x-feature-status-badge status="coming-soon" message="Q1 2025" />
```

### `tooltip` (string, optional)
Additional context shown when hovering over the badge.

**Default:** `null` (uses the message as tooltip if not specified)

**Example:**
```blade
<x-feature-status-badge
    status="unavailable"
    tooltip="Requires PHP 8.4 compatibility"
/>
```

### `strikethrough` (boolean, optional)
Applies a strikethrough effect to the badge text and reduces opacity.

**Default:** `false`

**Example:**
```blade
<x-feature-status-badge status="deprecated" :strikethrough="true" />
```

## Styling

The component uses Tailwind CSS utility classes with dark mode support:

### Color Scheme
- **Success (Green)**: Available features
- **Info (Blue)**: Beta features
- **Warning (Orange)**: Coming soon features
- **Danger (Red)**: Unavailable features
- **Gray**: Deprecated features

### Dark Mode
All colors automatically adapt to dark mode with appropriate contrast adjustments.

## Integration with Filament

The badge component is designed to work seamlessly with Filament admin pages and resources.

### In Filament Pages

```php
// app/Filament/Pages/DeveloperTools.php
public function getTools(): array
{
    return [
        [
            'name' => 'Laravel Horizon',
            'status' => 'unavailable',
            'strikethrough' => true,
            'statusMessage' => 'Coming Q1 2025',
            'tooltip' => 'Dependency conflict with Vonage SMS client (PHP 8.4).',
        ],
    ];
}
```

```blade
<!-- resources/views/filament/pages/developer-tools.blade.php -->
<x-feature-status-badge
    :status="$tool['status']"
    :message="$tool['statusMessage'] ?? null"
    :tooltip="$tool['tooltip'] ?? null"
    :strikethrough="$tool['strikethrough'] ?? false"
/>
```

### In Filament Resources

You can use the badge in custom columns or infolists:

```php
use Filament\Tables\Columns\ViewColumn;

ViewColumn::make('feature_status')
    ->view('filament.components.feature-status-badge')
    ->label('Status')
```

## Examples

### Example 1: Beta Feature

```blade
<x-feature-status-badge status="beta" />
```

Output: Blue badge with beaker icon showing "Beta"

### Example 2: Coming Soon Feature

```blade
<x-feature-status-badge
    status="coming-soon"
    message="Q2 2025"
    tooltip="Feature is currently in development"
/>
```

Output: Orange badge with clock icon showing "Q2 2025", hovering shows tooltip

### Example 3: Deprecated Feature (with Strikethrough)

```blade
<x-feature-status-badge
    status="deprecated"
    message="Legacy API v1"
    tooltip="Use API v2 instead. This will be removed in version 3.0"
    :strikethrough="true"
/>
```

Output: Gray badge with warning icon, strikethrough text showing "Legacy API v1"

### Example 4: Unavailable Feature (Horizon Use Case)

```blade
<div class="flex items-center gap-2">
    <span class="@if($tool['strikethrough']) line-through opacity-60 @endif">
        Laravel Horizon
    </span>

    <x-feature-status-badge
        status="unavailable"
        message="Coming Q1 2025"
        tooltip="Dependency conflict with Vonage SMS client (PHP 8.4). Actively monitoring for resolution."
        :strikethrough="true"
    />
</div>
```

Output: Feature name with strikethrough, red badge showing "Coming Q1 2025"

## Implementation Details

### Component Structure

The badge consists of two files:

1. **Component Class**: `app/Filament/Components/FeatureStatusBadge.php`
   - Handles logic for colors, icons, messages
   - Provides helper methods: `getColor()`, `getIcon()`, `getMessage()`, `getCssClasses()`

2. **Blade View**: `resources/views/filament/components/feature-status-badge.blade.php`
   - Renders the visual badge
   - Integrates with Alpine.js for tooltips
   - Uses Filament's icon component

### Registration

The component is registered in `AppServiceProvider`:

```php
// app/Providers/AppServiceProvider.php
public function boot(): void
{
    Blade::component('feature-status-badge', \App\Filament\Components\FeatureStatusBadge::class);
}
```

## Accessibility

- Uses semantic HTML with proper ARIA attributes
- Icons provide visual context but are not relied upon solely
- Tooltips are keyboard-accessible via Alpine.js
- Color contrast meets WCAG 2.1 AA standards in both light and dark modes

## Future Enhancements

Potential improvements for future versions:

1. **Clickable badges**: Add optional action when clicking badge
2. **Progress indicators**: Show percentage complete for features in development
3. **Changelog links**: Link to release notes or roadmap items
4. **Custom color schemes**: Allow teams to define their own status types
5. **Animation effects**: Subtle pulse or fade-in effects for new status changes

## Troubleshooting

### Badge Not Rendering

**Issue**: Component shows as plain text
**Solution**: Ensure the component is registered in `AppServiceProvider` and config cache is cleared:
```bash
php artisan config:clear
```

### Icons Not Showing

**Issue**: Icons appear as empty squares
**Solution**: Verify Heroicons are installed and Filament is properly configured:
```bash
composer require blade-ui-kit/blade-heroicons
php artisan filament:upgrade
```

### Tooltip Not Working

**Issue**: Tooltip doesn't appear on hover
**Solution**: Ensure Alpine.js is loaded. Check Filament panel configuration includes Alpine.js.

## Related Documentation

- [Developer Tools Page](../pages/developer-tools.md)
- [Filament Components Documentation](https://filamentphp.com/docs/3.x/support/blade-components)
- [Tailwind CSS Customization](https://tailwindcss.com/docs)

---

**Component Version:** 1.0
**Last Updated:** 2025-10-10
**Maintainer:** Commatix Development Team
