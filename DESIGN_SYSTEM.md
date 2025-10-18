# Commatix Design System & UI/UX Guide

## Overview

Commatix uses a modern **glassmorphism** design aesthetic inspired by South African design principles, built on Filament 4, TailwindCSS, and Alpine.js.

---

## Design Philosophy

### Core Principles

1. **Clarity First** - Information hierarchy should be immediately obvious
2. **Elegant Simplicity** - Minimal cognitive load, maximum efficiency
3. **South African UX Standards** - Right-aligned action buttons, local best practices
4. **Performance Aware** - Fast loading, smooth animations, optimized rendering
5. **Accessibility** - WCAG 2.1 AA compliant, keyboard navigation, screen reader friendly
6. **Multi-Tenant Awareness** - Visual distinction between tenants without confusion

---

## Color System

### Brand Colors (OKLCH)

Our primary brand uses the **OKLCH color space** for perceptual uniformity and modern color accuracy.

#### Commatix Primary (Blue-based)

```css
commatix-50:  oklch(0.98 0.02 200)  /* Lightest - backgrounds */
commatix-100: oklch(0.95 0.05 200)
commatix-200: oklch(0.90 0.08 200)
commatix-300: oklch(0.82 0.12 200)
commatix-400: oklch(0.74 0.15 200)
commatix-500: oklch(0.65 0.18 200)  /* PRIMARY - main brand color */
commatix-600: oklch(0.56 0.15 200)
commatix-700: oklch(0.47 0.12 200)
commatix-800: oklch(0.38 0.09 200)
commatix-900: oklch(0.29 0.06 200)
commatix-950: oklch(0.20 0.03 200)  /* Darkest - text on light bg */
```

#### South African Gold Accent

```css
sa-gold-500: oklch(0.8 0.12 85)  /* Use sparingly for emphasis */
```

#### Tenant-Specific Colors

```css
tenant-blue:   oklch(0.65 0.18 230)  /* Default tenant color */
tenant-green:  oklch(0.75 0.16 140)  /* Success, active tenants */
tenant-orange: oklch(0.75 0.15 45)   /* Warnings, alerts */
```

### Usage Guidelines

#### Primary Actions
- Use `commatix-500` to `commatix-700` for primary buttons
- Hover states: shift by 100 (e.g., 500 → 600)

#### Backgrounds
- Light mode: `commatix-50` to `commatix-100`
- Dark mode: `commatix-900` to `commatix-950`

#### Text
- Primary text: `commatix-950` (light mode), `commatix-50` (dark mode)
- Secondary text: `commatix-700` (light mode), `commatix-300` (dark mode)
- Muted text: `commatix-500`

#### Accent Usage
- **SA Gold**: Use for special highlights, achievements, premium features
- **Tenant Colors**: Use for tenant-specific UI elements, badges, status indicators

---

## Typography

### Font Family

```css
font-family: 'Figtree', system-ui, -apple-system, sans-serif
```

**Figtree** is a modern, readable sans-serif optimized for UI.

### Type Scale

```css
/* Headers */
text-3xl  /* Page titles: 30px */
text-2xl  /* Section headers: 24px */
text-xl   /* Subsection headers: 20px */
text-lg   /* Card titles: 18px */

/* Body */
text-base /* Primary body: 16px */
text-sm   /* Secondary body: 14px */
text-xs   /* Captions, labels: 12px */
```

### Font Weights

```css
font-light    (300) /* Rarely used, large titles only */
font-normal   (400) /* Body text */
font-medium   (500) /* Emphasized text */
font-semibold (600) /* Subheadings */
font-bold     (700) /* Headings, CTAs */
```

### Best Practices

- **Headings**: `font-bold` or `font-semibold`
- **Body text**: `font-normal`
- **Buttons**: `font-medium` or `font-semibold`
- **Labels**: `font-medium text-sm`
- **Captions**: `font-normal text-xs text-commatix-500`

---

## Spacing System

Follow TailwindCSS spacing scale (4px base unit):

```css
/* Common spacings */
gap-2   (8px)   /* Tight groups (icon + text) */
gap-4   (16px)  /* Standard spacing (form fields) */
gap-6   (24px)  /* Section spacing */
gap-8   (32px)  /* Major section spacing */

/* Padding */
p-4     (16px)  /* Cards, small containers */
p-6     (24px)  /* Standard cards */
p-8     (32px)  /* Large containers */

/* Margins */
mb-4    (16px)  /* Between form fields */
mb-6    (24px)  /* Between sections */
mb-8    (32px)  /* Between major sections */
```

---

## Glassmorphism

### Core Effect

```css
.glass-card {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 12px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}
```

### Variants

#### Subtle Glass (default cards)
```css
background: rgba(255, 255, 255, 0.7);
backdrop-filter: blur(10px);
```

#### Prominent Glass (modals, popovers)
```css
background: rgba(255, 255, 255, 0.85);
backdrop-filter: blur(20px);
```

#### Dark Glass (dark mode)
```css
background: rgba(0, 0, 0, 0.6);
backdrop-filter: blur(15px);
```

### Best Practices

- Use glass effects for **layered UI** (modals, dropdowns, cards over images)
- **Don't overuse** - not every element needs glass
- Ensure **text readability** - use sufficient background opacity
- Test on **low-end devices** - fallback to solid backgrounds if needed

---

## Animations

### Available Animations

```css
/* Fade in (entrance) */
animate-fade-in          /* 0.3s ease-in-out */

/* Slide up (entrance) */
animate-slide-up         /* 0.3s ease-out */

/* Floating effect (decorative) */
animate-glass-float      /* 6s infinite ease-in-out */

/* Metric counter (data visualization) */
animate-metric-up        /* 0.6s ease-out */
```

### Usage Guidelines

#### Page Load
```html
<div class="fade-in">
  <!-- Content appears smoothly -->
</div>
```

#### Card Entrance
```html
<div class="animate-slide-up">
  <!-- Cards slide up on load -->
</div>
```

#### Floating Elements
```html
<div class="animate-glass-float">
  <!-- Subtle floating effect -->
</div>
```

#### Data Metrics
```html
<div class="animate-metric-up">
  <!-- Numbers animate in -->
</div>
```

### Best Practices

- **Use sparingly** - animations should enhance, not distract
- **Respect user preferences** - `prefers-reduced-motion`
- **Performance first** - use `transform` and `opacity` (GPU-accelerated)
- **Meaningful motion** - animations should communicate state changes

---

## Components

### Buttons

#### Primary Button
```html
<button class="bg-commatix-500 hover:bg-commatix-600 text-white font-medium px-4 py-2 rounded-lg transition-colors">
  Primary Action
</button>
```

#### Secondary Button
```html
<button class="bg-commatix-100 hover:bg-commatix-200 text-commatix-700 font-medium px-4 py-2 rounded-lg transition-colors">
  Secondary Action
</button>
```

#### Danger Button
```html
<button class="bg-red-500 hover:bg-red-600 text-white font-medium px-4 py-2 rounded-lg transition-colors">
  Delete
</button>
```

#### Button Sizes
```html
<!-- Small -->
<button class="px-3 py-1.5 text-sm">Small</button>

<!-- Medium (default) -->
<button class="px-4 py-2 text-base">Medium</button>

<!-- Large -->
<button class="px-6 py-3 text-lg">Large</button>
```

### Cards

#### Standard Card
```html
<div class="bg-white rounded-xl shadow-sm p-6 border border-commatix-100">
  <h3 class="text-lg font-semibold mb-2">Card Title</h3>
  <p class="text-commatix-700">Card content goes here.</p>
</div>
```

#### Glass Card
```html
<div class="glass-card p-6">
  <h3 class="text-lg font-semibold mb-2">Glass Card</h3>
  <p class="text-commatix-700">Glassmorphism effect applied.</p>
</div>
```

### Forms

#### Input Field
```html
<div class="mb-4">
  <label class="block text-sm font-medium text-commatix-700 mb-2">
    Field Label
  </label>
  <input
    type="text"
    class="w-full px-4 py-2 border border-commatix-300 rounded-lg focus:ring-2 focus:ring-commatix-500 focus:border-transparent"
    placeholder="Enter value..."
  />
</div>
```

#### Form Actions (South African Standard)
```html
<footer class="flex justify-end gap-4 mt-6">
  <button class="secondary-button">Cancel</button>
  <button class="primary-button">Save</button>
</footer>
```

**Important**: Action buttons ALWAYS align to the right in South African UX.

### Loading States

#### Skeleton Loader
```html
<div class="animate-pulse">
  <div class="h-4 bg-commatix-200 rounded w-3/4 mb-2"></div>
  <div class="h-4 bg-commatix-200 rounded w-1/2"></div>
</div>
```

#### Spinner
```html
<div class="animate-spin h-8 w-8 border-4 border-commatix-500 border-t-transparent rounded-full"></div>
```

### Status Badges

```html
<!-- Success -->
<span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">
  Active
</span>

<!-- Warning -->
<span class="bg-tenant-orange/20 text-orange-800 px-3 py-1 rounded-full text-xs font-medium">
  Pending
</span>

<!-- Error -->
<span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-medium">
  Failed
</span>

<!-- Info -->
<span class="bg-commatix-100 text-commatix-800 px-3 py-1 rounded-full text-xs font-medium">
  Draft
</span>
```

---

## Layout Patterns

### Dashboard Grid
```html
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <!-- Cards go here -->
</div>
```

### Two-Column Layout
```html
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
  <div><!-- Left column --></div>
  <div><!-- Right column --></div>
</div>
```

### Sidebar Layout
```html
<div class="flex gap-6">
  <aside class="w-64 flex-shrink-0">
    <!-- Sidebar -->
  </aside>
  <main class="flex-1">
    <!-- Main content -->
  </main>
</div>
```

---

## South African UX Standards

### 1. Form Action Buttons
- **ALWAYS align to the right** (not left or center)
- Order: Cancel (left) → Primary Action (right)
- Minimum touch target: 44x44px (mobile)

### 2. Date Formats
```
Short:  DD/MM/YYYY  (05/12/2024)
Long:   5 December 2024
Time:   HH:MM (24-hour format)
```

### 3. Currency
```
R 1,250.00  (with space after R)
R1,250      (acceptable for compact displays)
```

### 4. Phone Numbers
```
+27 12 345 6789   (with spaces)
(012) 345 6789    (landline)
082 123 4567      (mobile)
```

### 5. Language Considerations
- English is primary
- Support for Afrikaans considered
- Clear, simple language (avoid jargon)

---

## Accessibility (WCAG 2.1 AA)

### Color Contrast

Minimum contrast ratios:
- **Normal text**: 4.5:1
- **Large text (18px+)**: 3:1
- **UI components**: 3:1

Our colors meet these standards:
```css
/* Good contrast pairs */
text-commatix-950 on bg-white       /* 13.2:1 */
text-white on bg-commatix-500       /* 4.8:1 */
text-commatix-700 on bg-commatix-50 /* 8.1:1 */
```

### Keyboard Navigation

- All interactive elements must be keyboard accessible
- Visible focus indicators (ring-2 ring-commatix-500)
- Logical tab order
- Escape to close modals/dropdowns

### Screen Readers

```html
<!-- Use aria-label for icon-only buttons -->
<button aria-label="Delete item">
  <svg>...</svg>
</button>

<!-- Use aria-describedby for additional context -->
<input aria-describedby="email-help" />
<small id="email-help">We'll never share your email.</small>

<!-- Use role for custom components -->
<div role="dialog" aria-modal="true">...</div>
```

### Focus Management

```css
/* Always show focus rings */
.focus-visible:focus {
  @apply ring-2 ring-commatix-500 ring-offset-2;
}
```

---

## Responsive Design

### Breakpoints

```css
sm:   640px   /* Small tablets */
md:   768px   /* Tablets */
lg:   1024px  /* Laptops */
xl:   1280px  /* Desktops */
2xl:  1536px  /* Large desktops */
```

### Mobile-First Approach

Always design for mobile first, then enhance for larger screens:

```html
<!-- Mobile: stacked, Tablet+: side-by-side -->
<div class="flex flex-col md:flex-row gap-4">
  <div class="w-full md:w-1/2">Left</div>
  <div class="w-full md:w-1/2">Right</div>
</div>
```

### Touch Targets

- Minimum size: **44x44px**
- Spacing between targets: **8px**
- Use `p-3` or larger for mobile buttons

---

## Performance Guidelines

### Optimize Images

```html
<!-- Use responsive images -->
<img
  src="image.jpg"
  srcset="image-sm.jpg 640w, image-md.jpg 1024w, image-lg.jpg 1920w"
  sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
  alt="Description"
  loading="lazy"
/>
```

### Lazy Loading

```html
<!-- Use wire:loading for Livewire -->
<div wire:loading class="animate-pulse">
  Loading...
</div>

<!-- Use loading="lazy" for images -->
<img src="..." loading="lazy" />
```

### Minimize Reflows

- Avoid layout thrashing
- Use `transform` instead of `top/left` for animations
- Batch DOM updates

---

## Multi-Tenancy UI

### Tenant Identification

```html
<!-- Subtle tenant branding in navigation -->
<div class="flex items-center gap-3">
  <div class="w-2 h-8 rounded-full bg-tenant-blue"></div>
  <span class="font-semibold">{{ tenant.name }}</span>
</div>
```

### Tenant-Specific Colors

```html
<!-- Use tenant color for accents -->
<div class="border-l-4" style="border-color: {{ tenant.color }}">
  Tenant-specific content
</div>
```

### Data Isolation Indicators

Always make it clear which tenant's data is being viewed:

```html
<div class="bg-tenant-blue/10 border border-tenant-blue/30 rounded-lg p-4 mb-6">
  <p class="text-sm text-commatix-700">
    <strong>{{ tenant.name }}</strong> data shown
  </p>
</div>
```

---

## Dark Mode

### Implementing Dark Mode

Use Filament's dark mode classes:

```html
<div class="bg-white dark:bg-commatix-900 text-commatix-950 dark:text-commatix-50">
  Content adapts to theme
</div>
```

### Dark Mode Colors

```css
/* Backgrounds */
dark:bg-commatix-950   /* Page background */
dark:bg-commatix-900   /* Card background */
dark:bg-commatix-800   /* Hover states */

/* Text */
dark:text-commatix-50  /* Primary text */
dark:text-commatix-300 /* Secondary text */
dark:text-commatix-500 /* Muted text */

/* Borders */
dark:border-commatix-700
```

---

## Common Mistakes to Avoid

### ❌ Don't Do This

```html
<!-- Hard to read text -->
<p class="text-commatix-300">Low contrast text</p>

<!-- Left-aligned form buttons (not SA standard) -->
<footer class="flex justify-start">
  <button>Save</button>
</footer>

<!-- Missing hover states -->
<button class="bg-commatix-500">No hover effect</button>

<!-- Inconsistent spacing -->
<div class="mb-2">
  <div class="mb-5">
    <div class="mb-3">Inconsistent</div>
  </div>
</div>
```

### ✅ Do This Instead

```html
<!-- High contrast text -->
<p class="text-commatix-700">Readable text</p>

<!-- Right-aligned form buttons (SA standard) -->
<footer class="flex justify-end gap-4">
  <button>Cancel</button>
  <button>Save</button>
</footer>

<!-- Proper hover states -->
<button class="bg-commatix-500 hover:bg-commatix-600 transition-colors">
  Interactive button
</button>

<!-- Consistent spacing (4, 6, 8 pattern) -->
<div class="mb-4">
  <div class="mb-6">
    <div class="mb-8">Consistent</div>
  </div>
</div>
```

---

## UI/UX Checklist

Use this checklist for every new feature:

### Visual Design
- [ ] Colors from defined palette (commatix, sa-gold, tenant)
- [ ] Typography scale followed (text-sm, text-base, text-lg, etc.)
- [ ] Spacing system used (gap-4, p-6, mb-8, etc.)
- [ ] Glass effects applied appropriately
- [ ] Animations are subtle and meaningful

### Components
- [ ] Buttons have hover/focus states
- [ ] Forms follow SA standard (right-aligned actions)
- [ ] Loading states implemented (skeleton or spinner)
- [ ] Status badges use semantic colors
- [ ] Cards use consistent padding and shadows

### Accessibility
- [ ] Color contrast meets WCAG AA (4.5:1)
- [ ] All interactive elements keyboard accessible
- [ ] Focus indicators visible
- [ ] ARIA labels for screen readers
- [ ] Text is readable at 200% zoom

### Responsive Design
- [ ] Mobile-first approach
- [ ] Touch targets minimum 44x44px
- [ ] Layout adapts at sm, md, lg breakpoints
- [ ] No horizontal scrolling on mobile
- [ ] Text remains readable on small screens

### Performance
- [ ] Images optimized and lazy-loaded
- [ ] Loading states for async operations
- [ ] No layout shifts (CLS)
- [ ] Smooth animations (60fps)

### Multi-Tenancy
- [ ] Tenant context clear to user
- [ ] Tenant-specific colors used
- [ ] Data isolation obvious
- [ ] No cross-tenant data leakage

### Filament Integration
- [ ] Follows Filament component patterns
- [ ] Uses Filament's form builder
- [ ] Integrates with Filament themes
- [ ] Livewire directives used correctly

---

## Tools & Resources

### Design Tools
- **Figma** - UI design mockups
- **TailwindCSS Docs** - https://tailwindcss.com
- **Filament Docs** - https://filamentphp.com/docs

### Color Tools
- **OKLCH Color Picker** - https://oklch.com
- **Contrast Checker** - https://webaim.org/resources/contrastchecker/

### Testing Tools
- **Lighthouse** - Performance and accessibility audits
- **axe DevTools** - Accessibility testing
- **Responsively** - Responsive design testing

### Code Quality
- Use `/ui-check [file-path]` to validate UI implementation
- Use `/design-system` for quick reference
- Use `/ui-expert` for design system guidance with browser preview

---

## Getting Help

- Run `/design-system` for a quick reference
- Run `/ui-check [file-path]` to validate your UI code
- Run `/ui-expert <task>` for specialized UI/UX guidance
- Consult this guide: `DESIGN_SYSTEM.md`
- Check Tailwind config: `tailwind.config.js`
- Review existing components: `resources/views/filament/components/`

---

**Version:** 1.0
**Last Updated:** 2025-10-15
**Maintained By:** Commatix Development Team
