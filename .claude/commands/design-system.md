---
description: Quick reference for Commatix design system
---

# Commatix Design System - Quick Reference

## Colors (OKLCH)

### Commatix Primary (Blue)
```
50:  oklch(0.98 0.02 200) - Lightest backgrounds
500: oklch(0.65 0.18 200) - PRIMARY brand color
950: oklch(0.20 0.03 200) - Darkest text
```

### SA Gold Accent
```
500: oklch(0.8 0.12 85) - Special highlights
```

### Tenant Colors
```
blue:   oklch(0.65 0.18 230)
green:  oklch(0.75 0.16 140)
orange: oklch(0.75 0.15 45)
```

## Typography

**Font:** Figtree sans-serif

**Scale:**
- `text-3xl` - Page titles (30px)
- `text-2xl` - Section headers (24px)
- `text-xl` - Subsection headers (20px)
- `text-lg` - Card titles (18px)
- `text-base` - Body text (16px)
- `text-sm` - Secondary text (14px)
- `text-xs` - Captions (12px)

**Weights:**
- `font-bold` - Headings
- `font-semibold` - Subheadings
- `font-medium` - Buttons, labels
- `font-normal` - Body text

## Spacing

**Common patterns:**
- `gap-2` (8px) - Tight groups
- `gap-4` (16px) - Form fields
- `gap-6` (24px) - Sections
- `gap-8` (32px) - Major sections

**Padding:**
- `p-4` (16px) - Small cards
- `p-6` (24px) - Standard cards
- `p-8` (32px) - Large containers

## Animations

```
animate-fade-in      - 0.3s ease-in-out
animate-slide-up     - 0.3s ease-out
animate-glass-float  - 6s infinite
animate-metric-up    - 0.6s ease-out
```

## Components

### Primary Button
```html
<button class="bg-commatix-500 hover:bg-commatix-600 text-white font-medium px-4 py-2 rounded-lg transition-colors">
  Action
</button>
```

### Glass Card
```html
<div class="glass-card p-6">
  Content
</div>
```

### Form Actions (SA Standard)
```html
<footer class="flex justify-end gap-4">
  <button>Cancel</button>
  <button>Save</button>
</footer>
```

### Status Badge
```html
<span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">
  Active
</span>
```

## Glassmorphism

```css
.glass-card {
  background: rgba(255, 255, 255, 0.7);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 12px;
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}
```

## South African UX

- Form buttons: **right-aligned**
- Button order: Cancel → Primary
- Date: DD/MM/YYYY
- Currency: R 1,250.00 (with space)
- Phone: +27 12 345 6789

## Accessibility

- Contrast: 4.5:1 minimum
- Touch targets: 44x44px
- Focus rings: `ring-2 ring-commatix-500`
- ARIA labels for icons

## Responsive Breakpoints

```
sm:  640px   - Small tablets
md:  768px   - Tablets
lg:  1024px  - Laptops
xl:  1280px  - Desktops
2xl: 1536px  - Large desktops
```

## Quick Commands

- `/ui-check [file]` - Validate implementation
- `/ui-expert <task>` - UI/UX specialist with browser
- `/design-system` - This quick reference
- Full guide: `DESIGN_SYSTEM.md`

---

**Need more details?** Read the full design system guide:
`/home/johndevsa/projects/commatix/DESIGN_SYSTEM.md`

**Need help?** Run `/ui-expert <your-task>` for specialized UI/UX guidance.
